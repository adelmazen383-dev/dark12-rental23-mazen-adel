<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\InvestorRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Client\ApartmentController as ClientApartmentController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\InspectionController;
use App\Http\Controllers\Client\ChatController as ClientChatController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ApartmentController as AdminApartmentController;
use App\Http\Controllers\Admin\InvestorController as AdminInvestorController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContractController as AdminContractController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\ReceptionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;

use App\Http\Controllers\Investor\DashboardController as InvestorDashboardController;
use App\Http\Controllers\Investor\ApartmentController as InvestorApartmentController;
use App\Http\Controllers\Investor\BookingController as InvestorBookingController;
use App\Http\Controllers\Investor\ContractController as InvestorContractController;
use App\Http\Controllers\Investor\InvoiceController as InvestorInvoiceController;
use App\Http\Controllers\Investor\ChatController as InvestorChatController;
use App\Http\Controllers\Investor\WalletController;
use App\Http\Controllers\Investor\SubscriptionController;
use App\Http\Controllers\Investor\ProfileController;

/*
|--------------------------------------------------------------------------
| Client Routes (Public - QR Code Entry)
|--------------------------------------------------------------------------
*/
Route::get('/', [ClientApartmentController::class, 'index'])->name('client.apartments.index');
Route::get('/about', function () { return view('client.about'); })->name('client.about');
Route::get('/services', function () { return view('client.services'); })->name('client.services');
Route::get('/sw.js', function () {
    $path = public_path('sw.js');
    $content = file_exists($path) ? file_get_contents($path) : '// Service Worker';
    return response($content, 200, [
        'Content-Type' => 'application/javascript',
        'Service-Worker-Allowed' => '/',
    ]);
});
Route::get('/manifest.json', function () {
    $path = public_path('manifest.json');
    $content = file_exists($path) ? file_get_contents($path) : '{}';
    return response($content, 200, [
        'Content-Type' => 'application/manifest+json',
    ]);
});
Route::get('/apartments/{apartment}', [ClientApartmentController::class, 'show'])->name('client.apartments.show');

// SEO: robots.txt dynamic
Route::get('/robots.txt', function () {
    $prefix = trim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/');
    $base = $prefix ? ('/' . $prefix) : '';
    $content  = "User-agent: *\n";
    $content .= "Allow: {$base}/\n";
    $content .= "Disallow: {$base}/admin/\n";
    $content .= "Disallow: {$base}/investor/\n";
    $content .= "Disallow: {$base}/my-chats\n";
    $content .= "Disallow: {$base}/chat/\n";
    $content .= "Disallow: {$base}/booking/\n";
    $content .= "Disallow: {$base}/login\n";
    $content .= "Disallow: {$base}/register\n";
    $content .= "Disallow: {$base}/investor/register\n\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
    return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
});

// SEO: Dynamic Sitemap
Route::get('/sitemap.xml', function () {
    $apartments = \App\Models\Apartment::visibleToClients()->latest('updated_at')->get();

    $urls = collect([
        ['loc' => url('/'),          'priority' => '1.0', 'changefreq' => 'daily',   'lastmod' => now()->toAtomString()],
        ['loc' => url('/about'),     'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => now()->toAtomString()],
        ['loc' => url('/services'),  'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => now()->toAtomString()],
    ]);

    foreach ($apartments as $apt) {
        $urls->push([
            'loc'        => url('/apartments/' . $apt->id),
            'priority'   => '0.9',
            'changefreq' => 'daily',
            'lastmod'    => $apt->updated_at ? $apt->updated_at->toAtomString() : now()->toAtomString(),
        ]);
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $url) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . e($url['loc']) . "</loc>\n";
        if (!empty($url['lastmod'])) {
            $xml .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
        }
        $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
        $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');

// Booking routes (protected with rate limiting)
Route::post('/apartments/{apartment}/book', [ClientBookingController::class, 'store'])->name('client.booking.store')->middleware('throttle:10,1');
Route::get('/booking/{booking:reference_number}', [ClientBookingController::class, 'show'])->name('client.booking.show');
Route::get('/booking/{booking:reference_number}/payment', [ClientBookingController::class, 'payment'])->name('client.booking.payment');
Route::post('/booking/{booking:reference_number}/upload-receipt', [ClientBookingController::class, 'uploadReceipt'])->name('client.booking.upload-receipt')->middleware('throttle:5,1');
Route::get('/booking/{booking:reference_number}/confirmation', [ClientBookingController::class, 'confirmation'])->name('client.booking.confirmation');

// Inspection routes
Route::post('/apartments/{apartment}/inspect', [InspectionController::class, 'store'])->name('client.inspection.store')->middleware('throttle:10,1');

// Client Chat routes
Route::get('/my-chats', [ClientChatController::class, 'index'])->name('client.chat.index');
Route::post('/my-chats/lookup', [ClientChatController::class, 'lookup'])->name('client.chat.lookup')->middleware('throttle:10,1');
Route::post('/apartments/{apartment}/chat/start', [ClientChatController::class, 'start'])->name('client.chat.start')->middleware('throttle:10,1');
Route::get('/chat/{token}', [ClientChatController::class, 'show'])->name('client.chat.show');
Route::post('/chat/{token}/send', [ClientChatController::class, 'sendMessage'])->name('client.chat.send')->middleware('throttle:30,1');
Route::get('/chat/{token}/messages', [ClientChatController::class, 'getMessages'])->name('client.chat.messages');
Route::put('/chat/{token}/messages/{message}', [ClientChatController::class, 'updateMessage'])->name('client.chat.messages.update')->middleware('throttle:30,1');
Route::delete('/chat/{token}/messages/{message}', [ClientChatController::class, 'deleteMessage'])->name('client.chat.messages.delete')->middleware('throttle:30,1');
Route::post('/chat/{token}/survey', [ClientChatController::class, 'submitSurvey'])->name('client.chat.survey')->middleware('throttle:10,1');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/investor/register', [InvestorRegisterController::class, 'showRegistrationForm'])->name('investor.register');
Route::post('/investor/register', [InvestorRegisterController::class, 'register']);

Route::get('/password/forgot', [ForgotPasswordController::class, 'showResetForm'])->name('password.request');
Route::post('/password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.send_otp');
Route::get('/password/verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify');
Route::post('/password/verify-otp', [ForgotPasswordController::class, 'verifyOtpAndReset'])->name('password.verify_submit');
Route::post('/password/reset', [ForgotPasswordController::class, 'verifyOtpAndReset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Apartments
    Route::resource('apartments', AdminApartmentController::class);
    Route::post('apartments/{apartment}/toggle-active', [AdminApartmentController::class, 'toggleActive'])->name('apartments.toggle-active');
    Route::post('apartments/{apartment}/toggle-featured', [AdminApartmentController::class, 'toggleFeatured'])->name('apartments.toggle-featured');
    Route::post('apartments/{apartment}/toggle-admin-approval', [AdminApartmentController::class, 'toggleAdminApproval'])->name('apartments.toggle-admin-approval');
    Route::delete('apartments/{apartment}/media/{media}', [AdminApartmentController::class, 'deleteMedia'])->name('apartments.delete-media');

    // Investors
    Route::resource('investors', AdminInvestorController::class)->only(['index', 'show', 'edit', 'update']);
    Route::get('investors/{investor}/documents/{type}', [AdminInvestorController::class, 'viewDocument'])->name('investors.document');
    Route::post('investors/{investor}/approve', [AdminInvestorController::class, 'approve'])->name('investors.approve');
    Route::post('investors/{investor}/reject', [AdminInvestorController::class, 'reject'])->name('investors.reject');
    Route::post('investors/{investor}/suspend', [AdminInvestorController::class, 'suspend'])->name('investors.suspend');
    Route::post('investors/{investor}/reactivate', [AdminInvestorController::class, 'reactivate'])->name('investors.reactivate');
    Route::post('investors/{investor}/toggle-admin-approval', [AdminInvestorController::class, 'toggleAdminApproval'])->name('investors.toggle-admin-approval');
    Route::post('investors/{investor}/add-balance', [AdminInvestorController::class, 'addBalance'])->name('investors.add-balance');
    Route::post('investors/{investor}/reset-password', [AdminInvestorController::class, 'resetPassword'])->name('investors.reset-password');

    // Bookings
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
    Route::post('bookings/{booking}/receipts/{receipt}/verify', [AdminBookingController::class, 'verifyReceipt'])->name('bookings.verify-receipt');
    Route::post('bookings/{booking}/receipts/{receipt}/reject', [AdminBookingController::class, 'rejectReceipt'])->name('bookings.reject-receipt');

    // Reception & Security Gatekeeper (Key Handover & Anti-Bypass)
    Route::get('reception', [ReceptionController::class, 'index'])->name('reception.index');
    Route::post('reception/check-in', [ReceptionController::class, 'checkInByCode'])->name('reception.check-in');
    Route::post('reception/check-out/{booking}', [ReceptionController::class, 'checkOut'])->name('reception.check-out');
    Route::post('reception/record-unregistered-deal', [ReceptionController::class, 'recordUnregisteredDeal'])->name('reception.record-unregistered-deal');

    // Rental Contracts (Special revenue stream)
    Route::resource('contracts', AdminContractController::class)->except(['edit', 'update', 'destroy']);
    Route::get('contracts/{contract}/print', [AdminContractController::class, 'print'])->name('contracts.print');
    Route::post('contracts/{contract}/status', [AdminContractController::class, 'updateStatus'])->name('contracts.update-status');

    // Invoices (Special revenue stream)
    Route::resource('invoices', AdminInvoiceController::class)->except(['edit', 'update', 'destroy']);
    Route::get('invoices/{invoice}/print', [AdminInvoiceController::class, 'print'])->name('invoices.print');
    Route::post('invoices/{invoice}/status', [AdminInvoiceController::class, 'updateStatus'])->name('invoices.update-status');

    // Centralized Chat Oversight & Supervision
    Route::get('chats', [AdminChatController::class, 'index'])->name('chats.index');
    Route::get('chats/{conversation}', [AdminChatController::class, 'show'])->name('chats.show');
    Route::post('chats/{conversation}/send', [AdminChatController::class, 'sendMessage'])->name('chats.send');
    Route::get('chats/{conversation}/messages', [AdminChatController::class, 'getMessages'])->name('chats.messages');
    Route::post('chats/{conversation}/toggle-status', [AdminChatController::class, 'toggleStatus'])->name('chats.toggle-status');

    // Inspections
    Route::get('inspections', [AdminDashboardController::class, 'inspections'])->name('inspections.index');
    Route::post('inspections/{inspection}/schedule', [AdminDashboardController::class, 'scheduleInspection'])->name('inspections.schedule');
    Route::post('inspections/{inspection}/complete', [AdminDashboardController::class, 'completeInspection'])->name('inspections.complete');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('bank-accounts', SettingsController::class)->only(['store', 'update', 'destroy'])->names('bank-accounts');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Audit Log
    Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // Notifications
    Route::get('notifications', [AdminDashboardController::class, 'notifications'])->name('notifications.index');
});

/*
|--------------------------------------------------------------------------
| Investor Routes
|--------------------------------------------------------------------------
*/
Route::prefix('investor')->name('investor.')->middleware(['auth', 'investor'])->group(function () {
    Route::get('/', [InvestorDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Apartments
    Route::resource('apartments', InvestorApartmentController::class);
    Route::post('apartments/{apartment}/upload-media', [InvestorApartmentController::class, 'uploadMedia'])->name('apartments.upload-media');
    Route::delete('apartments/{apartment}/media/{media}', [InvestorApartmentController::class, 'deleteMedia'])->name('apartments.delete-media');
    Route::post('apartments/{apartment}/blocked-dates', [InvestorApartmentController::class, 'storeBlockedDate'])->name('apartments.blocked-dates.store');
    Route::delete('apartments/{apartment}/blocked-dates/{blockedDate}', [InvestorApartmentController::class, 'deleteBlockedDate'])->name('apartments.blocked-dates.destroy');

    // Phone / Live Notifications Polling Endpoint
    Route::get('api/unread-alerts', [InvestorDashboardController::class, 'unreadAlerts'])->name('api.unread-alerts');
    Route::get('api/vapid-key', [InvestorDashboardController::class, 'getVapidKey'])->name('api.vapid-key');
    Route::post('api/push-subscribe', [InvestorDashboardController::class, 'subscribePush'])->name('api.push-subscribe');
    Route::post('api/test-push', [InvestorDashboardController::class, 'testPush'])->name('api.test-push');

    // Bookings
    Route::get('bookings', [InvestorBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [InvestorBookingController::class, 'show'])->name('bookings.show');

    // Rental Contracts
    Route::resource('contracts', InvestorContractController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('contracts/{contract}/print', [InvestorContractController::class, 'print'])->name('contracts.print');

    // Invoices
    Route::resource('invoices', InvestorInvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('invoices/{invoice}/print', [InvestorInvoiceController::class, 'print'])->name('invoices.print');

    // Chat with Tenants
    Route::get('chats', [InvestorChatController::class, 'index'])->name('chats.index');
    Route::get('chats/{conversation}', [InvestorChatController::class, 'show'])->name('chats.show');
    Route::post('chats/{conversation}/send', [InvestorChatController::class, 'sendMessage'])->name('chats.send');
    Route::get('chats/{conversation}/messages', [InvestorChatController::class, 'getMessages'])->name('chats.messages');
    Route::put('chats/{conversation}/messages/{message}', [InvestorChatController::class, 'updateMessage'])->name('chats.messages.update');
    Route::delete('chats/{conversation}/messages/{message}', [InvestorChatController::class, 'deleteMessage'])->name('chats.messages.delete');
    Route::post('chats/{conversation}/survey', [InvestorChatController::class, 'submitSurvey'])->name('chats.survey');

    // Wallet
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');

    // Subscription
    Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
});

// Secure Storage route for serverless environments (handles uploaded files and persistent SVG fallbacks)
Route::get('/storage/{path}', function ($path) {
    // 1. Strict Path Traversal and Null Byte Defense
    if (str_contains($path, '..') || str_contains($path, "\0") || str_starts_with($path, '/') || str_starts_with($path, '\\')) {
        \App\Services\AuditService::log('security_path_traversal_blocked', null, null, [
            'attempted_path' => $path,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        abort(403, 'مسار غير مصرح به.');
    }

    // 2. Block sensitive file extensions from being served directly
    $forbiddenExtensions = ['php', 'phtml', 'phar', 'env', 'sqlite', 'db', 'key', 'sql', 'sh', 'bat', 'cmd', 'git', 'json', 'yml', 'yaml', 'lock', 'config', 'log', 'htaccess', 'htpasswd', 'bak', 'backup', 'tmp', 'swp'];
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($extension, $forbiddenExtensions, true)) {
        \App\Services\AuditService::log('security_forbidden_extension_blocked', null, null, [
            'attempted_path' => $path,
            'ip' => request()->ip(),
        ]);
        abort(403, 'امتداد الملف محظور.');
    }

    $candidates = [
        storage_path('app/public/' . $path),
        '/tmp/storage/app/public/' . $path,
        base_path('storage/app/public/' . $path),
        public_path('storage/' . $path),
    ];

    $allowedPrefixes = [
        storage_path('app/public'),
        base_path('storage/app/public'),
        public_path('storage'),
        '/tmp/storage/app/public',
    ];

    foreach ($candidates as $file) {
        if (file_exists($file) && is_file($file)) {
            $realFile = realpath($file);
            if ($realFile) {
                // Ensure the canonical path is strictly inside allowed storage directories
                $isPermitted = false;
                foreach ($allowedPrefixes as $prefix) {
                    $realPrefix = realpath($prefix) ?: $prefix;
                    if (str_starts_with($realFile, $realPrefix)) {
                        $isPermitted = true;
                        break;
                    }
                }

                if ($isPermitted) {
                    $mime = @mime_content_type($realFile) ?: 'application/octet-stream';
                    return response()->file($realFile, [
                        'Content-Type' => $mime,
                        'Cache-Control' => 'public, max-age=86400',
                        'X-Content-Type-Options' => 'nosniff',
                    ]);
                }
            }
        }
    }

    // Dynamic High-Fidelity SVG fallbacks when file is not yet uploaded or container was recycled
    if (str_contains($path, 'license')) {
        return \App\Services\FallbackMediaService::renderLicenseDocumentSvg($path);
    }

    if (str_contains($path, 'id_') || str_contains($path, 'ids')) {
        return \App\Services\FallbackMediaService::renderIdDocumentSvg($path);
    }

    if (str_contains($path, 'apartments') || str_contains($path, 'images')) {
        return \App\Services\FallbackMediaService::renderApartmentImageSvg($path);
    }

    if (str_contains($path, 'receipt')) {
        return \App\Services\FallbackMediaService::renderReceiptSvg($path);
    }

    return \App\Services\FallbackMediaService::renderGenericDocSvg($path);
})->where('path', '.*');
