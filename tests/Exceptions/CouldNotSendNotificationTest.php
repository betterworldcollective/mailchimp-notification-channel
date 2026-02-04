<?php

namespace NotificationChannels\Mailchimp\Test\Exceptions;

use NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification;
use PHPUnit\Framework\TestCase;

class CouldNotSendNotificationTest extends TestCase
{
    /** @test */
    public function it_creates_exception_with_full_response_details(): void
    {
        $response = [
            [
                'email' => 'test@example.com',
                'status' => 'rejected',
                'reject_reason' => 'hard-bounce',
            ],
        ];

        $exception = CouldNotSendNotification::serviceRespondedWithAnError($response);

        $this->assertInstanceOf(CouldNotSendNotification::class, $exception);
        $this->assertEquals(
            "Mailchimp responded with status 'rejected' for 'test@example.com': hard-bounce",
            $exception->getMessage()
        );
    }

    /** @test */
    public function it_handles_missing_response_fields(): void
    {
        $response = [[]];

        $exception = CouldNotSendNotification::serviceRespondedWithAnError($response);

        $this->assertEquals(
            "Mailchimp responded with status 'unknown' for 'unknown': unknown",
            $exception->getMessage()
        );
    }

    /** @test */
    public function it_handles_partial_response_fields(): void
    {
        $response = [
            [
                'status' => 'rejected',
            ],
        ];

        $exception = CouldNotSendNotification::serviceRespondedWithAnError($response);

        $this->assertEquals(
            "Mailchimp responded with status 'rejected' for 'unknown': unknown",
            $exception->getMessage()
        );
    }

    /** @test */
    public function it_is_throwable(): void
    {
        $response = [
            [
                'email' => 'test@example.com',
                'status' => 'rejected',
                'reject_reason' => 'invalid-sender',
            ],
        ];

        $this->expectException(CouldNotSendNotification::class);

        throw CouldNotSendNotification::serviceRespondedWithAnError($response);
    }
}
