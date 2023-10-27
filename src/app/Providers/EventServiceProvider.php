<?php

namespace App\Providers;

use App\Events\ExpertConsultationRequestEvent;
use App\Events\ExpertPaymentStatusEvent;
use App\Events\ExpertVerificationEvent;
use App\Listeners\ExpertConsultationRequestHandler;
use App\Listeners\ExpertPaymentStatusHandler;
use App\Listeners\ExpertVerificationHandler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        ExpertVerificationEvent::class => [
            ExpertVerificationHandler::class,
        ],

        ExpertPaymentStatusEvent::class => [
            ExpertPaymentStatusHandler::class,
        ],

        ExpertConsultationRequestEvent::class => [
            ExpertConsultationRequestHandler::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
