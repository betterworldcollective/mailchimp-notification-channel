<?php

namespace NotificationChannels\Mailchimp;

use Illuminate\Notifications\Notification;
use NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification;

class MailchimpChannel
{
    public function __construct(private Mailchimp $mailchimp)
    {
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification
     */
    public function send(mixed $notifiable, Notification $notification): ?array
    {
        /** @var MailchimpMessage $message */
        $message = $notification->toMailchimp($notifiable);

        if (!$message instanceof MailchimpMessage) {
            return null;
        }

        $response = $this->mailchimp->sendMessage($message);

        if (isset($response[0]['status']) && $response[0]['status'] === 'rejected') {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }

        return $response;
    }
}
