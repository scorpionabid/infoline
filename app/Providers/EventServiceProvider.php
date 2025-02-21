<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Log\Events\MessageLogged;

// Import custom event classes
use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;

// Additional event classes
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Laravel\Sanctum\Events\TokenAuthenticated;
use App\Listeners\LogTokenAuthentication;

use App\Events\Auth\FailedLoginAttemptEvent;
use App\Events\Auth\PasswordResetEvent;
use App\Listeners\Auth\HandleFailedLogin;
use App\Listeners\Auth\LogPasswordReset;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Authentication Events
        LoginEvent::class => [
            LogSuccessfulLogin::class,
        ],
        LogoutEvent::class => [
            LogSuccessfulLogout::class,
        ],

        // Laravel Built-in Events
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Sanctum Token Events
        TokenAuthenticated::class => [
            LogTokenAuthentication::class,
        ],
        
        FailedLoginAttemptEvent::class => [
            HandleFailedLogin::class,
        ],
        
        PasswordResetEvent::class => [
            LogPasswordReset::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array<class-string>
     */
    protected $subscribe = [
        // Add any event subscribers here
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Log Event Listener
        Event::listen(MessageLogged::class, function (MessageLogged $event) {
            // Clean old logs every time a message is logged
            $this->cleanOldLogs();
        });

        // Optional: Register additional event listeners dynamically
        $this->registerCustomEventListeners();
    }

    /**
     * Clean up old log files periodically.
     */
    private function cleanOldLogs(): void
    {
        $logPath = storage_path('logs');
        $threshold = now()->subHours(24); // Keep logs for 24 hours instead of 20 minutes

        // Check and remove old log files
        foreach (glob($logPath . '/*.log') as $file) {
            if (filemtime($file) < $threshold->timestamp) {
                try {
                    unlink($file);
                } catch (\Exception $e) {
                    // Log any errors during log file deletion
                    \Log::warning("Could not delete log file: {$file}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Dynamically register additional event listeners.
     */
    private function registerCustomEventListeners(): void
    {
        // Example of dynamic event listener registration
        Event::listen('user.created', function ($user) {
            // Perform actions when a user is created
            \Log::info("New user created", ['user_id' => $user->id]);
        });

        // You can add more dynamic event listeners here
    }
}