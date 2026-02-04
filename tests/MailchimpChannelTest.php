<?php

namespace NotificationChannels\Mailchimp\Test;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification;
use NotificationChannels\Mailchimp\Mailchimp;
use NotificationChannels\Mailchimp\MailchimpChannel;
use NotificationChannels\Mailchimp\MailchimpMessage;
use PHPUnit\Framework\TestCase;

class MailchimpChannelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private Mailchimp|Mockery\MockInterface $mailchimp;
    private Dispatcher|Mockery\MockInterface $events;
    private MailchimpChannel $channel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailchimp = Mockery::mock(Mailchimp::class);
        $this->events = Mockery::mock(Dispatcher::class);
        $this->channel = new MailchimpChannel($this->mailchimp, $this->events);
    }

    /** @test */
    public function it_can_send_a_notification(): void
    {
        $notifiable = new TestNotifiable();
        $notification = new TestNotification();

        $expectedResponse = [
            [
                'email' => 'test@example.com',
                'status' => 'sent',
                '_id' => 'abc123',
            ],
        ];

        $this->mailchimp
            ->shouldReceive('sendMessage')
            ->once()
            ->with(Mockery::type(MailchimpMessage::class))
            ->andReturn($expectedResponse);

        $response = $this->channel->send($notifiable, $notification);

        $this->assertEquals($expectedResponse, $response);
    }

    /** @test */
    public function it_returns_null_when_notification_does_not_return_mailchimp_message(): void
    {
        $notifiable = new TestNotifiable();
        $notification = new InvalidNotification();

        $response = $this->channel->send($notifiable, $notification);

        $this->assertNull($response);
    }

    /** @test */
    public function it_dispatches_failed_event_when_exception_is_thrown(): void
    {
        $notifiable = new TestNotifiable();
        $notification = new TestNotification();

        $this->mailchimp
            ->shouldReceive('sendMessage')
            ->once()
            ->andThrow(new CouldNotSendNotification("Mailchimp responded with status 'rejected' for 'test@example.com': hard-bounce"));

        $this->events
            ->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(\Illuminate\Notifications\Events\NotificationFailed::class));

        $response = $this->channel->send($notifiable, $notification);

        $this->assertNull($response);
    }

    /** @test */
    public function it_does_not_throw_exception_for_queued_status(): void
    {
        $notifiable = new TestNotifiable();
        $notification = new TestNotification();

        $queuedResponse = [
            [
                'email' => 'test@example.com',
                'status' => 'queued',
                '_id' => 'abc123',
            ],
        ];

        $this->mailchimp
            ->shouldReceive('sendMessage')
            ->once()
            ->andReturn($queuedResponse);

        $response = $this->channel->send($notifiable, $notification);

        $this->assertEquals($queuedResponse, $response);
    }
}

class TestNotifiable
{
    public string $email = 'test@example.com';
    public string $name = 'Test User';
}

class TestNotification extends Notification
{
    public function toMailchimp($notifiable): MailchimpMessage
    {
        return (new MailchimpMessage())
            ->templateName('test-template')
            ->subject('Test Subject')
            ->to($notifiable->email, $notifiable->name)
            ->from('sender@example.com', 'Sender')
            ->addMergeTag('NAME', $notifiable->name);
    }
}

class InvalidNotification extends Notification
{
    public function toMailchimp($notifiable): string
    {
        return 'not a MailchimpMessage';
    }
}
