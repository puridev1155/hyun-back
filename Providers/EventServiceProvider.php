<?php

namespace App\Providers;

use App\Events\MemberRegistered;
use App\Events\MemberWithdrawned;
use App\Listeners\UpdateMemberStat;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        MemberRegistered::class => [
            UpdateMemberStat::class
        ],

        MemberWithdrawned::class => [
            UpdateMemberStat::class
        ],

        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Kakao\KakaoExtendSocialite::class.'@handle',
            \SocialiteProviders\Line\LineExtendSocialite::class.'@handle',
            \SocialiteProviders\Instagram\InstagramExtendSocialite::class.'@handle',
            \SocialiteProviders\Apple\AppleExtendSocialite::class.'@handle',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
