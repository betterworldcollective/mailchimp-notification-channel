<?php

namespace NotificationChannels\Mailchimp\Data;

class Recipient
{

    public function __construct(private string $email, private ?string $name, private ?string $type = 'to')
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
            ['email' => $this->email, 'name' => $this->name, 'type' => $this->type],
            static fn ($value): bool => $value !== null
        );
    }


    public function getType(): ?string
    {
        return $this->type;
    }
}
