<?php

namespace NotificationChannels\Mailchimp\Test\Data;

use NotificationChannels\Mailchimp\Data\Sender;
use PHPUnit\Framework\TestCase;

class SenderTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated_with_email_and_name(): void
    {
        $sender = new Sender('sender@example.com', 'My App');

        $this->assertEquals('sender@example.com', $sender->getFromEmail());
        $this->assertEquals('My App', $sender->getFromName());
    }

    /** @test */
    public function it_can_be_instantiated_with_email_only(): void
    {
        $sender = new Sender('sender@example.com', null);

        $this->assertEquals('sender@example.com', $sender->getFromEmail());
        $this->assertNull($sender->getFromName());
    }

    /** @test */
    public function it_converts_to_array_with_name(): void
    {
        $sender = new Sender('sender@example.com', 'My App');

        $this->assertEquals([
            'from_email' => 'sender@example.com',
            'from_name' => 'My App',
        ], $sender->toArray());
    }

    /** @test */
    public function it_converts_to_array_without_name(): void
    {
        $sender = new Sender('sender@example.com', null);

        $this->assertEquals([
            'from_email' => 'sender@example.com',
        ], $sender->toArray());
    }

    /** @test */
    public function it_filters_out_null_values_in_to_array(): void
    {
        $sender = new Sender('sender@example.com', null);
        $array = $sender->toArray();

        $this->assertArrayNotHasKey('from_name', $array);
    }
}
