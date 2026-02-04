<?php

namespace NotificationChannels\Mailchimp\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    public static function serviceRespondedWithAnError(array $response): self
    {
        $status = $response[0]['status'] ?? 'unknown';
        $rejectReason = $response[0]['reject_reason'] ?? 'unknown';
        $email = $response[0]['email'] ?? 'unknown';

        return new static("Mailchimp responded with status '{$status}' for '{$email}': {$rejectReason}");
    }
}
