<?php

namespace NotificationChannels\Mailchimp\Data;

class Sender
{

    public function __construct(private string $fromEmail, private ?string $fromName)
    {

    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function toArray(): array
    {
        return array_filter(
            ['from_email' => $this->fromEmail, 'from_name' => $this->fromName],
            static fn ($value): bool => $value !== null
        );
    }

}
