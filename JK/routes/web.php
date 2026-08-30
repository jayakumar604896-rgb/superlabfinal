<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Api\CouponApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\EnquiryApiController;
use App\Http\Controllers\Api\PackageApiController;
use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\TestCategoryApiController;
use App\Http\Controllers\Api\TestimonialApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\RazorpayApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Admin\ServiceReviewController;
use App\Http\Controllers\Api\CustomPackageApiController;
use App\Http\Controllers\Api\ServiceReviewApiController;
use App\Http\Controllers\Admin\CustomerController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{id}/force', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::resource('/users', UserController::class);

    // Customers
    Route::post('/customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('/customers/{id}/force', [CustomerController::class, 'forceDelete'])->name('customers.force-delete');
    Route::post('/customers/{id}/vitals', [CustomerController::class, 'addVital'])->name('customers.vitals.store');
    Route::delete('/customers/vitals/{vitalId}', [CustomerController::class, 'deleteVital'])->name('customers.vitals.destroy');
    Route::post('/bookings/{bookingId}/report', [CustomerController::class, 'uploadReport'])->name('bookings.report.upload');
    Route::resource('/customers', CustomerController::class);

    // Roles
    Route::post('/roles/{id}/restore', [RoleController::class, 'restore'])->name('roles.restore');
    Route::delete('/roles/{id}/force', [RoleController::class, 'forceDelete'])->name('roles.force-delete');
    Route::resource('/roles', RoleController::class);

    // Categories
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::resource('/categories', CategoryController::class);

    // Services
    Route::post('/services/{id}/restore', [ServiceController::class, 'restore'])->name('services.restore');
    Route::delete('/services/{id}/force', [ServiceController::class, 'forceDelete'])->name('services.force-delete');
    Route::resource('/services', ServiceController::class);

    // Pages
    Route::post('/pages/{id}/restore', [PageController::class, 'restore'])->name('pages.restore');
    Route::delete('/pages/{id}/force', [PageController::class, 'forceDelete'])->name('pages.force-delete');
    Route::resource('/pages', PageController::class);

    // Blogs
    Route::post('/blogs/{id}/restore', [BlogController::class, 'restore'])->name('blogs.restore');
    Route::delete('/blogs/{id}/force', [BlogController::class, 'forceDelete'])->name('blogs.force-delete');
    Route::resource('/blogs', BlogController::class);

    // Gallery
    Route::post('/gallery/{id}/restore', [GalleryController::class, 'restore'])->name('gallery.restore');
    Route::delete('/gallery/{id}/force', [GalleryController::class, 'forceDelete'])->name('gallery.force-delete');
    Route::resource('/gallery', GalleryController::class);

    // Enquiries
    Route::post('/enquiries/{id}/restore', [EnquiryController::class, 'restore'])->name('enquiries.restore');
    Route::delete('/enquiries/{id}/force', [EnquiryController::class, 'forceDelete'])->name('enquiries.force-delete');
    Route::resource('/enquiries', EnquiryController::class)->except(['create', 'store', 'edit', 'update']);

    // Testimonials
    Route::post('/testimonials/{id}/restore', [TestimonialController::class, 'restore'])->name('testimonials.restore');
    Route::delete('/testimonials/{id}/force', [TestimonialController::class, 'forceDelete'])->name('testimonials.force-delete');
    Route::resource('/testimonials', TestimonialController::class);

    Route::get('/service-reviews', [ServiceReviewController::class, 'index'])->name('service-reviews.index');
    Route::get('/service-reviews/{id}', [ServiceReviewController::class, 'show'])->name('service-reviews.show');
    Route::put('/service-reviews/{id}/status', [ServiceReviewController::class, 'updateStatus'])->name('service-reviews.update-status');
    Route::delete('/service-reviews/{id}', [ServiceReviewController::class, 'destroy'])->name('service-reviews.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Locations
    Route::post('/locations/{id}/restore', [LocationController::class, 'restore'])->name('locations.restore');
    Route::delete('/locations/{id}/force', [LocationController::class, 'forceDelete'])->name('locations.force-delete');
    Route::resource('/locations', LocationController::class);

    // Packages
    Route::post('/packages/{id}/restore', [PackageController::class, 'restore'])->name('packages.restore');
    Route::delete('/packages/{id}/force', [PackageController::class, 'forceDelete'])->name('packages.force-delete');
    Route::resource('/packages', PackageController::class);

    // Payment Types
    Route::post('/payment-types/{id}/restore', [PaymentTypeController::class, 'restore'])->name('payment-types.restore');
    Route::delete('/payment-types/{id}/force', [PaymentTypeController::class, 'forceDelete'])->name('payment-types.force-delete');
    Route::resource('/payment-types', PaymentTypeController::class);

    // Bookings
    Route::post('/bookings/{id}/restore', [BookingController::class, 'restore'])->name('bookings.restore');
    Route::delete('/bookings/{id}/force', [BookingController::class, 'forceDelete'])->name('bookings.force-delete');
    Route::resource('/bookings', BookingController::class);

    // Payments
    Route::post('/payments/{id}/restore', [PaymentController::class, 'restore'])->name('payments.restore');
    Route::delete('/payments/{id}/force', [PaymentController::class, 'forceDelete'])->name('payments.force-delete');
    Route::resource('/payments', PaymentController::class);

    // Payment Gateways
    Route::post('/payment-gateways/{id}/restore', [PaymentGatewayController::class, 'restore'])->name('payment-gateways.restore');
    Route::delete('/payment-gateways/{id}/force', [PaymentGatewayController::class, 'forceDelete'])->name('payment-gateways.force-delete');
    Route::resource('/payment-gateways', PaymentGatewayController::class);

    Route::post('/coupons/{id}/restore', [CouponController::class, 'restore'])->name('coupons.restore');
    Route::delete('/coupons/{id}/force', [CouponController::class, 'forceDelete'])->name('coupons.force-delete');
    Route::resource('/coupons', CouponController::class);

    // Legacy catalog admin URLs (redirect to unified Categories + Services)
    Route::any('/test-categories/{path?}', fn (?string $path = null) => redirect()
        ->route('admin.categories.index')
        ->with('info', 'Test categories are now managed under Categories.'))
        ->where('path', '.*')
        ->name('test-categories.legacy');

    Route::any('/tests/{path?}', fn (?string $path = null) => redirect()
        ->route('admin.services.index')
        ->with('info', 'Tests are now managed under Services.'))
        ->where('path', '.*')
        ->name('tests.legacy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/api/tests/{slug}', [ServiceApiController::class, 'show']);
Route::get('/api/packages', [PackageApiController::class, 'index']);

Route::group(['prefix' => 'api/v1'], function () {
    Route::get('/test-categories', [TestCategoryApiController::class, 'index']);
    Route::get('/test-categories/{slug}', [TestCategoryApiController::class, 'show']);
    Route::get('/categories', [CategoryApiController::class, 'index']);

    Route::get('/services', [ServiceApiController::class, 'index']);
    Route::get('/services/{slug}', [ServiceApiController::class, 'show']);

    Route::get('/packages', [PackageApiController::class, 'index']);
    Route::get('/packages/{slug}', [PackageApiController::class, 'show']);

    Route::get('/testimonials', [TestimonialApiController::class, 'index']);

    Route::get('/reviews', [ServiceReviewApiController::class, 'index']);
    Route::post('/reviews', [ServiceReviewApiController::class, 'store'])
        ->middleware('auth.customer.optional');

    Route::get('/custom-packages', [CustomPackageApiController::class, 'index'])
        ->middleware('auth.customer');
    Route::get('/custom-packages/{id}', [CustomPackageApiController::class, 'show']);
    Route::post('/custom-packages', [CustomPackageApiController::class, 'store'])
        ->middleware('auth.customer.optional');

    Route::post('/bookings', [BookingApiController::class, 'store'])
        ->middleware('auth.customer.optional');

    Route::get('/coupons/available', [CouponApiController::class, 'available'])
        ->middleware('auth.customer.optional');

    Route::post('/coupons/validate', [CouponApiController::class, 'validateCode'])
        ->middleware('auth.customer.optional');

    Route::post('/reports/lookup', [ReportApiController::class, 'lookup']);

    Route::post('/razorpay/order', [RazorpayApiController::class, 'createOrder']);
    Route::post('/razorpay/verify', [RazorpayApiController::class, 'verify'])
        ->middleware('auth.customer.optional');

    Route::get('/payment-gateways', [PaymentApiController::class, 'index']);
    Route::post('/payment/order', [PaymentApiController::class, 'createOrder']);
    Route::post('/payment/verify', [PaymentApiController::class, 'verify'])
        ->middleware('auth.customer.optional');

    Route::post('/enquiries', [EnquiryApiController::class, 'store']);

    Route::post('/register', [CustomerApiController::class, 'register']);
    Route::post('/login', [CustomerApiController::class, 'login']);

    Route::post('/logout', [CustomerApiController::class, 'logout'])->middleware('auth.customer');

    Route::get('/customer/profile-data', [CustomerApiController::class, 'profileData'])->middleware('auth.customer');
    Route::put('/customer/profile', [CustomerApiController::class, 'updateProfile'])->middleware('auth.customer');
});
