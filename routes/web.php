<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Pincode check API (no auth required)
Route::get('/api/check-pincode/{pincode}', function (string $pincode) {
    return response()->json([
        'available' => \App\Services\PincodeService::isServiceable($pincode),
        'city' => \App\Services\PincodeService::getCity($pincode),
    ]);
})->name('api.check-pincode');

// Language switch
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'hi', 'mr'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Public routes
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/services/{slug}', [PublicController::class, 'serviceDetail'])->name('services.detail');
Route::get('/how-it-works', [PublicController::class, 'howItWorks'])->name('how-it-works');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/faq', [PublicController::class, 'faq'])->name('faq');
Route::get('/privacy-policy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicController::class, 'terms'])->name('terms');
Route::get('/refund-policy', [PublicController::class, 'refundPolicy'])->name('refund-policy');

// Auth routes
require __DIR__.'/auth.php';

// Authenticated profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', fn () => view('customer.dashboard'))->name('dashboard');
    Route::get('/book', fn () => view('customer.book'))->name('book');
    Route::get('/bookings', fn () => view('customer.bookings'))->name('bookings');
    Route::get('/bookings/{id}', fn ($id) => view('customer.booking-detail', ['id' => $id]))->name('bookings.show');
    Route::get('/addresses', fn () => view('customer.addresses'))->name('addresses');
    Route::get('/reviews', fn () => view('customer.reviews'))->name('reviews');
});

// Daimaa routes
Route::middleware(['auth', 'role:daimaa'])->prefix('daimaa')->name('daimaa.')->group(function () {
    Route::get('/dashboard', fn () => view('daimaa.dashboard'))->name('dashboard');
    Route::get('/bookings', fn () => view('daimaa.bookings'))->name('bookings');
    Route::get('/schedule', fn () => view('daimaa.schedule'))->name('schedule');
    Route::get('/payouts', fn () => view('daimaa.payouts'))->name('payouts');
    Route::get('/profile', fn () => view('daimaa.profile'))->name('profile');
});

// Daimaa registration (accessible to any authenticated user without a role requirement)
Route::middleware('auth')->get('/daimaa/register', fn () => view('daimaa.register'))->name('daimaa.register');

// Admin routes
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    Route::get('/customers', fn () => view('admin.customers'))->name('customers');
    Route::get('/daimaas', fn () => view('admin.daimaas'))->name('daimaas');
    Route::get('/daimaa-skills', fn () => view('admin.daimaa-skills'))->name('daimaa-skills');
    Route::get('/kyc', fn () => view('admin.kyc'))->name('kyc');
    Route::get('/services', fn () => view('admin.services'))->name('services');
    Route::get('/packages', fn () => view('admin.packages'))->name('packages');
    Route::get('/add-ons', fn () => view('admin.add-ons'))->name('add-ons');
    Route::get('/bundles', fn () => view('admin.bundles'))->name('bundles');
    Route::get('/bookings', fn () => view('admin.bookings'))->name('bookings');
    Route::get('/assignments', fn () => view('admin.assignments'))->name('assignments');
    Route::get('/payments', fn () => view('admin.payments'))->name('payments');
    Route::get('/payouts', fn () => view('admin.payouts'))->name('payouts');
    Route::get('/coupons', fn () => view('admin.coupons'))->name('coupons');
    Route::get('/pincodes', fn () => view('admin.pincodes'))->name('pincodes');
    Route::get('/cms', fn () => view('admin.cms'))->name('cms');
    Route::get('/faqs', fn () => view('admin.faqs'))->name('faqs');
    Route::get('/testimonials', fn () => view('admin.testimonials'))->name('testimonials');
    Route::get('/settings', fn () => view('admin.settings'))->name('settings');
    Route::get('/audit-logs', fn () => view('admin.audit-logs'))->name('audit-logs');
});

// Post-login redirect based on role
Route::middleware('auth')->get('/dashboard', function () {
    return match(auth()->user()->role) {
        'admin', 'super_admin' => redirect()->route('admin.dashboard'),
        'daimaa' => redirect()->route('daimaa.dashboard'),
        default => redirect()->route('customer.dashboard'),
    };
})->name('dashboard');
