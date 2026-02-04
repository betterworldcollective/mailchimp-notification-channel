<?php

namespace NotificationChannels\Mailchimp;

use GuzzleHttp\Exception\ClientException;
use MailchimpTransactional\ApiClient as MailchimpClient;
use NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification;

class Mailchimp
{
    public function __construct(private MailchimpClient $mailchimpClient)
    {
    }

    /**
     * @throws \NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification
     */
    public function sendMessage(MailchimpMessage $message): mixed
    {
        $response =  $this->mailchimpClient->messages->sendTemplate($message->getMessageBody());

        if($response instanceof ClientException){
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }


        if (isset($response[0]->status) && $response[0]->status === 'rejected') {
            throw CouldNotSendNotification::emailWasRejected($response);
        }

        return $response;
    }
}
