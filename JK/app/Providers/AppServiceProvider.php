<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Repositories\Contracts\UserRepositoryInterface::class, \App\Repositories\Eloquent\UserRepository::class);
        $this->app->bind(\App\Repositories\Contracts\RoleRepositoryInterface::class, \App\Repositories\Eloquent\RoleRepository::class);
        $this->app->bind(\App\Repositories\Contracts\CategoryRepositoryInterface::class, \App\Repositories\Eloquent\CategoryRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ServiceRepositoryInterface::class, \App\Repositories\Eloquent\ServiceRepository::class);
        $this->app->bind(\App\Repositories\Contracts\PageRepositoryInterface::class, \App\Repositories\Eloquent\PageRepository::class);
        $this->app->bind(\App\Repositories\Contracts\BlogRepositoryInterface::class, \App\Repositories\Eloquent\BlogRepository::class);
        $this->app->bind(\App\Repositories\Contracts\GalleryRepositoryInterface::class, \App\Repositories\Eloquent\GalleryRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ContactEnquiryRepositoryInterface::class, \App\Repositories\Eloquent\ContactEnquiryRepository::class);
        $this->app->bind(\App\Repositories\Contracts\TestimonialRepositoryInterface::class, \App\Repositories\Eloquent\TestimonialRepository::class);
        $this->app->bind(\App\Repositories\Contracts\SettingRepositoryInterface::class, \App\Repositories\Eloquent\SettingRepository::class);
        $this->app->bind(\App\Repositories\Contracts\ActivityLogRepositoryInterface::class, \App\Repositories\Eloquent\ActivityLogRepository::class);
        $this->app->bind(\App\Repositories\Contracts\LocationRepositoryInterface::class, \App\Repositories\Eloquent\LocationRepository::class);
        $this->app->bind(\App\Repositories\Contracts\FooterLocationRepositoryInterface::class, \App\Repositories\Eloquent\FooterLocationRepository::class);
        $this->app->bind(\App\Repositories\Contracts\PackageRepositoryInterface::class, \App\Repositories\Eloquent\PackageRepository::class);
        $this->app->bind(\App\Repositories\Contracts\PaymentTypeRepositoryInterface::class, \App\Repositories\Eloquent\PaymentTypeRepository::class);
        $this->app->bind(\App\Repositories\Contracts\BookingRepositoryInterface::class, \App\Repositories\Eloquent\BookingRepository::class);
        $this->app->bind(\App\Repositories\Contracts\PaymentRepositoryInterface::class, \App\Repositories\Eloquent\PaymentRepository::class);
        $this->app->bind(\App\Repositories\Contracts\PaymentGatewayRepositoryInterface::class, \App\Repositories\Eloquent\PaymentGatewayRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            function ($event) {
                $activityLog = app(\App\Repositories\Contracts\ActivityLogRepositoryInterface::class);
                $activityLog->log('Login', "User {$event->user->name} logged in successfully.");
            }
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function ($event) {
                if ($event->user) {
                    $activityLog = app(\App\Repositories\Contracts\ActivityLogRepositoryInterface::class);
                    $activityLog->log('Logout', "User {$event->user->name} logged out.");
                }
            }
        );
    }
}
