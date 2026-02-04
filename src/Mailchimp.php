<?php

namespace NotificationChannels\Mailchimp;

use MailchimpTransactional\ApiClient as MailchimpClient;

class Mailchimp
{

    public function __construct(private MailchimpClient $mailchimpClient)
    {

    }

    public function sendMessage(MailchimpMessage $message)
    {

       $this->mailchimpClient->messages->sendTemplate()
    }
}
