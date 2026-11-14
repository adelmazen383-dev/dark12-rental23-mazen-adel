<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Investor, Apartment, ApartmentMedia, Wallet};
use App\Services\{AuditService, NotificationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InvestorRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // When explicitly choosing to register a new investor account, log out any existing session so they can enter their own credentials cleanly
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return view('auth.investor-register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            // Account info
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            // Investor info
            'full_name' => 'required|string|max:255',
            'id_number' => 'required|string|max:20',
            'id_image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            // Bank info
            'bank_name' => 'required|string|max:100',
            'bank_account_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            // Tourism license
            'license_number' => 'required|string|max:50',
            'license_name' => 'required|string|max:255',
            'license_image' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
            // Initial Apartment info
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
            'apartment_images.*' => 'nullable|file|mimes:jpeg,png,jpg|max:10240',
            'apartment_video' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
        ], [
            'required' => 'حقل :attribute مطلوب.',
            'unique' => ':attribute مسجل مسبقاً في النظام.',
            'confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'mimes' => 'صيغة الملف غير مدعومة.',
        ]);

        $investor = DB::transaction(function () use ($request, $validated) {
            // 1. Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'investor',
                'is_active' => true,
                'phone' => $validated['phone'],
            ]);

            // 2. Upload Investor Documents
            $idImagePath = $request->file('id_image')->store('investors/ids', 'public');
            $licenseImagePath = $request->file('license_image')->store('investors/licenses', 'public');

            // 3. Create Investor
            $investor = Investor::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'id_number' => $validated['id_number'],
                'id_image' => $idImagePath,
                'phone' => $validated['phone'],
                'whatsapp' => $validated['whatsapp'],
                'bank_name' => $validated['bank_name'],
                'bank_account_name' => $validated['bank_account_name'],
                'iban' => $validated['iban'],
                'license_number' => $validated['license_number'],
                'license_name' => $validated['license_name'],
                'license_image' => $licenseImagePath,
                'status' => 'pending',
            ]);

            // 4. Create Wallet
            Wallet::create([
                'investor_id' => $investor->id,
                'balance' => 0.00,
            ]);

            // 5. Create Initial Apartment (inactive until investor approved)
            $apartment = Apartment::create([
                'investor_id' => $investor->id,
                'apartment_number' => $validated['apartment_number'],
                'floor_number' => $validated['floor_number'],
                'description' => $validated['description'] ?? '',
                'rooms_count' => $validated['rooms_count'],
                'living_rooms_count' => $validated['living_rooms_count'],
                'bathrooms_count' => $validated['bathrooms_count'],
                'view_type' => $validated['view_type'] ?? '',
                'features' => $validated['features'] ?? '',
                'daily_price' => $validated['daily_price'],
                'insurance_amount' => $validated['insurance_amount'],
                'extra_fees' => $validated['extra_fees'] ?? 0,
                'checkin_time' => $validated['checkin_time'],
                'checkout_time' => $validated['checkout_time'],
                'is_available' => true,
                'is_active' => false, // Will become active after admin approval
            ]);

            // Upload Apartment Images
            if ($request->hasFile('apartment_images')) {
                foreach ($request->file('apartment_images') as $index => $image) {
                    $imagePath = $image->store('apartments/images', 'public');
                    ApartmentMedia::create([
                        'apartment_id' => $apartment->id,
                        'type' => 'image',
                        'file_path' => $imagePath,
                        'sort_order' => $index,
                    ]);
                }
            }

            // Upload Apartment Video
            if ($request->hasFile('apartment_video')) {
                $videoPath = $request->file('apartment_video')->store('apartments/videos', 'public');
                ApartmentMedia::create([
                    'apartment_id' => $apartment->id,
                    'type' => 'video',
                    'file_path' => $videoPath,
                    'sort_order' => 99,
                ]);
            }

            AuditService::log('investor_registered', $investor, null, ['investor_id' => $investor->id, 'name' => $investor->full_name]);

            return $investor;
        });

        // Notify Admin via WhatsApp
        NotificationService::sendWhatsApp(
            \App\Models\Setting::getValue('admin_whatsapp', '0500000000'),
            "🏢 طلب تسجيل مستثمر جديد\nالاسم: {$investor->full_name}\nالجوال: {$investor->phone}\nرقم الشقة: {$request->apartment_number}\nيرجى مراجعة الطلب والترخيص في لوحة الإدارة.",
            'admin',
            'investor',
            $investor->id
        );

        return redirect()->route('login')->with('success', 'تم تقديم طلب التسجيل بنجاح! طلبك الآن قيد المراجعة والتدقيق لدى إدارة البرج، وسيتم إشعارك فور الاعتماد.');
    }
}
