<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, ApartmentMedia, Investor};
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Apartment::with(['investor', 'media']);

        if ($request->filled('floor')) {
            $query->where('floor_number', $request->floor);
        }
        if ($request->filled('investor_id')) {
            $query->where('investor_id', $request->investor_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        if ($request->filled('featured')) {
            $query->where('is_featured_on_homepage', $request->featured === '1');
        }

        $apartments = $query->orderByDesc('is_featured_on_homepage')->latest()->paginate(15);
        $investors = Investor::where('status', 'approved')->get();
        $floors = Apartment::select('floor_number')->distinct()->orderBy('floor_number')->pluck('floor_number');

        return view('admin.apartments.index', compact('apartments', 'investors', 'floors'));
    }

    public function create()
    {
        $investors = Investor::where('status', 'approved')->get();
        return view('admin.apartments.create', compact('investors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_id' => 'required|exists:investors,id',
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
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['admin_approved_override'] = $request->boolean('admin_approved_override', false);
        $validated['is_featured_on_homepage'] = $request->boolean('is_featured_on_homepage', false);
        $validated['homepage_order'] = (int) $request->input('homepage_order', 0);

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

        AuditService::log('apartment_created', $apartment, null, $apartment->toArray());

        return redirect()->route('admin.apartments.index')->with('success', 'تمت إضافة الشقة بنجاح.');
    }

    public function show(Apartment $apartment)
    {
        $apartment->load(['investor', 'media', 'bookings' => fn($q) => $q->latest()->take(10)]);
        return view('admin.apartments.show', compact('apartment'));
    }

    public function edit(Apartment $apartment)
    {
        $investors = Investor::where('status', 'approved')->get();
        return view('admin.apartments.edit', compact('apartment', 'investors'));
    }

    public function update(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'investor_id' => 'required|exists:investors,id',
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
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
        ]);

        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['admin_approved_override'] = $request->boolean('admin_approved_override', false);
        $validated['is_featured_on_homepage'] = $request->boolean('is_featured_on_homepage', false);
        $validated['homepage_order'] = (int) $request->input('homepage_order', 0);

        $old = $apartment->toArray();
        $apartment->update($validated);

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

        AuditService::log('apartment_updated', $apartment, $old, $apartment->toArray());

        return redirect()->route('admin.apartments.index')->with('success', 'تم تحديث بيانات الشقة بنجاح.');
    }

    public function toggleActive(Apartment $apartment)
    {
        $apartment->update(['is_active' => !$apartment->is_active]);
        AuditService::log('apartment_toggled_active', $apartment, null, ['is_active' => $apartment->is_active]);
        return back()->with('success', 'تم تغيير حالة ظهور الشقة بنجاح.');
    }

    public function toggleFeatured(Apartment $apartment)
    {
        $apartment->update([
            'is_featured_on_homepage' => !$apartment->is_featured_on_homepage,
        ]);
        AuditService::log('apartment_homepage_featured_toggled', $apartment, null, [
            'is_featured_on_homepage' => $apartment->is_featured_on_homepage,
        ]);
        $msg = $apartment->is_featured_on_homepage 
            ? 'تم تثبيت الشقة لتظهر في واجهة الصفحة الأولى للنزلاء بنجاح (سري دون علم المالك).'
            : 'تم إلغاء تثبيت الشقة من الصفحة الأولى.';
        return back()->with('success', $msg);
    }

    public function toggleAdminApproval(Apartment $apartment)
    {
        $apartment->update([
            'admin_approved_override' => !$apartment->admin_approved_override,
        ]);
        AuditService::log('apartment_admin_approval_toggled', $apartment, null, [
            'admin_approved_override' => $apartment->admin_approved_override,
        ]);
        $msg = $apartment->admin_approved_override 
            ? "تم منح موافقة الإدارة على إظهار الشقة {$apartment->apartment_number} استثنائياً للنزلاء."
            : "تم إلغاء موافقة الإدارة الاستثنائية للشقة {$apartment->apartment_number}.";
        return back()->with('success', $msg);
    }

    public function deleteMedia(Apartment $apartment, ApartmentMedia $media)
    {
        if ($media->apartment_id === $apartment->id) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
            return back()->with('success', 'تم حذف الملف بنجاح.');
        }
        return back()->with('error', 'الملف غير مرتبط بهذه الشقة.');
    }

    public function destroy(Apartment $apartment)
    {
        $apartment->delete();
        AuditService::log('apartment_deleted', null, $apartment->toArray(), null);
        return redirect()->route('admin.apartments.index')->with('success', 'تم حذف الشقة بنجاح.');
    }
}
