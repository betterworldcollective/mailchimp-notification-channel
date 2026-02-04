<?php

namespace NotificationChannels\Mailchimp;

use MailchimpTransactional\ApiClient as MailchimpClient;

class Mailchimp
{
    public function __construct(private MailchimpClient $mailchimpClient)
    {
    }

    public function sendMessage(MailchimpMessage $message): mixed
    {
        return $this->mailchimpClient->messages->sendTemplate($message->getMessageBody());
    }
}
