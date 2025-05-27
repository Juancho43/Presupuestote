<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\PaymentCreated;
use App\Listeners\UpdatePeopleBalance;

class EventServiceProvider extends ServiceProvider
{



    protected $listen = [
        PaymentCreated::class => [
            UpdatePeopleBalance::class,
        ],
    ];


    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }



    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
