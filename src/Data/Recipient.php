<?php

namespace NotificationChannels\Mailchimp\Data;

use Illuminate\Contracts\Support\Arrayable;

class Recipient
{

    public function __construct(private string $email, private ?string $name)
    {

    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return array_filter(
            ['email' => $this->email, 'name' => $this->name],
            static fn ($value): bool => $value !== null
        );
    }
}
