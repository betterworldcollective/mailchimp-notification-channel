<?php

namespace NotificationChannels\Mailchimp;

use Illuminate\Support\ServiceProvider;
use MailchimpTransactional\ApiClient;

class MailchimpServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->mergeConfigFrom(__DIR__.'/../config/mailchimp-notification-channel.php', 'mailchimp-notification-channel');

        $this->publishes([
            __DIR__.'/../config/mailchimp-notification-channel.php' => config_path('mailchimp-notification-channel.php'),
        ]);
    }
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        $this->app->when(MailchimpChannel::class)
            ->needs(Mailchimp::class)
            ->give(function () {

                $apiClient = new ApiClient();
                $apiClient->setApiKey(config('mailchimp-notification-channel.api_key'));
                return new Mailchimp($apiClient);
            });

    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
