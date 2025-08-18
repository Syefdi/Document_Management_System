<?php

namespace App\Providers;

use App\Repositories\Contracts\NotificationScheduleInterface;
use App\Repositories\Implementation\NotificationScheduleRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            NotificationScheduleInterface::class,
            NotificationScheduleRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       
    }
}
