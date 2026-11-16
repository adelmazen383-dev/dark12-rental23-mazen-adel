<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, ApartmentBlockedDate, ApartmentMedia};
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApartmentController extends Controller
{
    public function index()
    {
        $investor = auth()->user()->investor;
        $apartments = $investor->apartments()->with('media')->latest()->paginate(10);

        return view('investor.apartments.index', compact('apartments', 'investor'));
    }

    public function create()
    {
        $investor = auth()->user()->investor;
        if ($investor->status !== 'approved') {
            return redirect()->route('investor.apartments.index')
                ->with('error', 'لا يمكنك إضافة شقق جديدة حتى يتم اعتماد حسابك من الإدارة.');
        }

        return view('investor.apartments.create');
    }

    public function store(Request $request)
    {
        $investor = auth()->user()->investor;
        if ($investor->status !== 'approved') {
            abort(403);
        }

        $validated = $request->validate([
            'apartment_number' => 'required|string|max:10',
            'floor_number' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'rooms_count' => 'required|integer|min:1',
            'living_rooms_count' => 'required|integer|min:0',
            'bathrooms_count' => 'required|integer|min:1',
            'view_type' => 'nullable|string|max:100',
            'features' => 'nullable|string',
            'daily_price' => 'required|numeric|min:0',
            'insurance_amount' => 'required|numeric|min:0',
            'extra_fees' => 'nullable|numeric|min:0',
            'checkin_time' => 'required',
            'checkout_time' => 'required',
            'images.*' => 'nullable|image|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
        ]);

        $validated['investor_id'] = $investor->id;
        $validated['is_available'] = true;
        $validated['is_active'] = true;

        $apartment = Apartment::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('apartments/images', 'public');
                ApartmentMedia::create([
                    'apartment_id' => $apartment->id,
                    'type' => 'image',
                    'file_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('apartments/videos', 'public');
            ApartmentMedia::create([
                'apartment_id' => $apartment->id,
                'type' => 'video',
                'file_path' => $path,
                'sort_order' => 99,
            ]);
        }

        AuditService::log('investor_added_apartment', $apartment);

        return redirect()->route('investor.apartments.index')->with('success', 'تمت إضافة الشقة بنجاح.');
    }

    public function show(Apartment $apartment)
    {
        $investor = auth()->user()->investor;
        if ($apartment->investor_id !== $investor->id) {
            abort(403);
        }

        $apartment->load(['media', 'blockedDates' => fn($q) => $q->orderBy('start_date', 'desc'), 'bookings' => fn($q) => $q->latest()->take(10)]);
        return view('investor.apartments.show', compact('apartment'));
    }

    public function edit(Apartment $apartment)
    {
        $investor = auth()->user()->investor;
        if ($apartment->investor_id !== $investor->id) {
            abort(403);
        }

        return view('investor.apartments.edit', compact('apartment'));
    }

    public function update(Request $request, Apartment $apartment)
    {
        $investor = auth()->user()->investor;
        if ($apartment->investor_id !== $investor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'view_type' => 'nullable|string|max:100',
            'features' => 'nullable|string',
            'daily_price' => 'required|numeric|min:0',
            'insurance_amount' => 'required|numeric|min:0',
            'extra_fees' => 'nullable|numeric|min:0',
            'checkin_time' => 'required',
            'checkout_time' => 'required',
            'is_available' => 'boolean',
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);

        $apartment->update($validated);

        AuditService::log('investor_updated_apartment', $apartment);

        return redirect()->route('investor.apartments.index')->with('success', 'تم تحديث بيانات الشقة بنجاح.');
    }

    public function uploadMedia(Request $request, Apartment $apartment)
    {
        $investor = auth()->user()->investor;
        if ($apartment->investor_id !== $investor->id) {
            abort(403);
        }

        $request->validate([
            'images.*' => 'nullable|image|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
        ]);

        if ($request->hasFile('images')) {
            $currentCount = $apartment->images()->count();
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('apartments/images', 'public');
                ApartmentMedia::create([
                    'apartment_id' => $apartment->id,
                    'type' => 'image',
                    'file_path' => $path,
                    'sort_order' => $currentCount + $index,
                ]);
            }
        }

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('apartments/videos', 'public');
            ApartmentMedia::create([
                'apartment_id' => $apartment->id,
                'type' => 'video',
                'file_path' => $path,
                'sort_order' => 99,
            ]);
        }

        return back()->with('success', 'تم رفع الوسائط بنجاح.');
    }

    public function deleteMedia(Apartment $apartment, ApartmentMedia $media)
    {
        $investor = auth()->user()->investor;
        if ($apartment->investor_id !== $investor->id || $media->apartment_id !== $apartment->id) {
            abort(403);
        }

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return back()->with('success', 'تم حذف الملف بنجاح.');
    }

    /**
     * Store a blocked date period for this apartment
     */
    public function storeBlockedDate(Request $request, Apartment $apartment)
    {
        $investor = auth()->user()->investor;
        if ($apartment->investor_id !== $investor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
        ], [
            'start_date.required' => 'تاريخ البداية مطلوب',
            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون مساوياً أو بعد تاريخ البداية',
            'reason.required' => 'يرجى تحديد سبب الإغلاق',
        ]);

        $validated['apartment_id'] = $apartment->id;
        $validated['investor_id'] = $investor->id;

        $blocked = ApartmentBlockedDate::create($validated);

        AuditService::log('investor_blocked_apartment_dates', $apartment, null, [
            'start_date' => $blocked->start_date->toDateString(),
            'end_date' => $blocked->end_date->toDateString(),
            'reason' => $blocked->reason,
        ]);

        return back()->with('success', 'تم إغلاق وتحديد فترة عدم التوفر بنجاح، ولن يتم قبول أي حجوزات في هذه الفترة.');
    }

    /**
     * Remove a blocked date period (re-open dates)
     */
    public function deleteBlockedDate(Apartment $apartment, ApartmentBlockedDate $blockedDate)
    {
        $investor = auth()->user()->investor;
        if ($apartment->investor_id !== $investor->id || $blockedDate->apartment_id !== $apartment->id) {
            abort(403);
        }

        $blockedDate->delete();

        AuditService::log('investor_unblocked_apartment_dates', $apartment);

        return back()->with('success', 'تم فتح التواريخ وإتاحتها للحجز من جديد.');
    }
}
