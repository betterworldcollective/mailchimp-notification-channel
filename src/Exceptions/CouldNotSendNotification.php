<?php

namespace NotificationChannels\Mailchimp\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends Exception
{

    public static function serviceRespondedWithAnError(ClientException $exception)
    {
       return new static($exception->getMessage(), $exception->getCode());
    }

    public static function emailWasRejected(array $response): self
    {
        $status = $response[0]->status ?? 'unknown';
        $rejectReason = $response[0]->reject_reason ?? 'unknown';
        $email = $response[0]->email ?? 'unknown';

        return new static("Mailchimp responded with status '{$status}' for '{$email}': {$rejectReason}");
    }
}
