<?php

namespace NotificationChannels\Mailchimp;

use NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;

class MailchimpChannel
{
    public function __construct(private Mailchimp $mailchimp)
    {
        // Initialisation code here
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        //$response = [a call to the api of your notification send]

        /** @var \NotificationChannels\Mailchimp\MailchimpMessage $message */
        $message = $notification->toMailchimp($notifiable);

        $response = $this->mailchimp->send($details);

//        if ($response->error) { // replace this by the code need to check for errors
//            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
//        }
    }
}
