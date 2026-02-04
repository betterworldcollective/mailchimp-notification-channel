<?php

namespace NotificationChannels\Mailchimp\Test\Data;

use NotificationChannels\Mailchimp\Data\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_with_email_and_name(): void
    {
        $recipient = new Recipient('john@example.com', 'John Doe');

        $this->assertEquals('john@example.com', $recipient->getEmail());
        $this->assertEquals('John Doe', $recipient->getName());
    }

    /** @test */
    public function it_can_be_instantiated_with_email_only(): void
    {
        $recipient = new Recipient('john@example.com', null);

        $this->assertEquals('john@example.com', $recipient->getEmail());
        $this->assertNull($recipient->getName());
    }

    /** @test */
    public function it_converts_to_array_with_name(): void
    {
        $recipient = new Recipient('john@example.com', 'John Doe');

        $this->assertEquals([
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ], $recipient->toArray());
    }

    /** @test */
    public function it_converts_to_array_without_name(): void
    {
        $recipient = new Recipient('john@example.com', null);

        $this->assertEquals([
            'email' => 'john@example.com',
        ], $recipient->toArray());
    }

    /** @test */
    public function it_filters_out_null_values_in_to_array(): void
    {
        $recipient = new Recipient('john@example.com', null);
        $array = $recipient->toArray();

        $this->assertArrayNotHasKey('name', $array);
    }
}
