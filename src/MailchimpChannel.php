<?php

namespace NotificationChannels\Mailchimp;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;

class MailchimpChannel
{
    public function __construct(private Mailchimp $mailchimp,  protected Dispatcher $events)
    {
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @return array|null
     */
    public function send(mixed $notifiable, Notification $notification): ?array
    {
        /** @var MailchimpMessage $message */
        $message = $notification->toMailchimp($notifiable);

        if (!$message instanceof MailchimpMessage) {
            return null;
        }

        if(is_null($message->getTo())){
            $to = $notifiable->routeNotificationFor('mailchimp', $notification);
            $message->to($to['email'], $to['name']);
        }

        try {
            return $this->mailchimp->sendMessage($message);
        } catch (Exception $exception){
            $event = new NotificationFailed(
                $notifiable,
                $notification,
                'mailchimp',
                ['message' => $exception->getMessage(), 'exception' => $exception]
            );

            $this->events->dispatch($event);
        }
        return null;
    }
}
