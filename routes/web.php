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
Route::get('/apartments/{apartment}', [ClientApartmentController::class, 'show'])->name('client.apartments.show');

// Booking routes
Route::post('/apartments/{apartment}/book', [ClientBookingController::class, 'store'])->name('client.booking.store');
Route::get('/booking/{booking:reference_number}', [ClientBookingController::class, 'show'])->name('client.booking.show');
Route::get('/booking/{booking:reference_number}/payment', [ClientBookingController::class, 'payment'])->name('client.booking.payment');
Route::post('/booking/{booking:reference_number}/upload-receipt', [ClientBookingController::class, 'uploadReceipt'])->name('client.booking.upload-receipt');
Route::get('/booking/{booking:reference_number}/confirmation', [ClientBookingController::class, 'confirmation'])->name('client.booking.confirmation');

// Inspection routes
Route::post('/apartments/{apartment}/inspect', [InspectionController::class, 'store'])->name('client.inspection.store');

// Client Chat routes
Route::post('/apartments/{apartment}/chat/start', [ClientChatController::class, 'start'])->name('client.chat.start');
Route::get('/chat/{token}', [ClientChatController::class, 'show'])->name('client.chat.show');
Route::post('/chat/{token}/send', [ClientChatController::class, 'sendMessage'])->name('client.chat.send');
Route::get('/chat/{token}/messages', [ClientChatController::class, 'getMessages'])->name('client.chat.messages');
Route::post('/chat/{token}/accept-offer', [ClientChatController::class, 'acceptOffer'])->name('client.chat.accept-offer');
Route::post('/chat/{token}/survey', [ClientChatController::class, 'submitSurvey'])->name('client.chat.survey');

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
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

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

    // Apartments
    Route::resource('apartments', InvestorApartmentController::class);
    Route::post('apartments/{apartment}/upload-media', [InvestorApartmentController::class, 'uploadMedia'])->name('apartments.upload-media');
    Route::delete('apartments/{apartment}/media/{media}', [InvestorApartmentController::class, 'deleteMedia'])->name('apartments.delete-media');

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
    Route::post('chats/{conversation}/offer', [InvestorChatController::class, 'makeOffer'])->name('chats.offer');
    Route::post('chats/{conversation}/survey', [InvestorChatController::class, 'submitSurvey'])->name('chats.survey');

    // Wallet
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');

    // Subscription
    Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
});
