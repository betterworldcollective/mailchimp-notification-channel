<?php

namespace NotificationChannels\Mailchimp\Test\Exceptions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use NotificationChannels\Mailchimp\Exceptions\CouldNotSendNotification;
use PHPUnit\Framework\TestCase;

class CouldNotSendNotificationTest extends TestCase
{
    /** @test */
    public function it_creates_exception_from_client_exception(): void
    {
        $request = new Request('POST', 'https://api.mailchimp.com');
        $response = new Response(400, [], 'Bad Request');
        $clientException = new ClientException('Error message', $request, $response);

        $exception = CouldNotSendNotification::serviceRespondedWithAnError($clientException);

        $this->assertInstanceOf(CouldNotSendNotification::class, $exception);
        $this->assertEquals('Error message', $exception->getMessage());
    }

    /** @test */
    public function it_creates_exception_for_rejected_email_with_full_response(): void
    {
        $response = [
            (object) [
                'email' => 'test@example.com',
                'status' => 'rejected',
                'reject_reason' => 'hard-bounce',
            ],
        ];

        $exception = CouldNotSendNotification::emailWasRejected($response);

        $this->assertInstanceOf(CouldNotSendNotification::class, $exception);
        $this->assertEquals(
            "Mailchimp responded with status 'rejected' for 'test@example.com': hard-bounce",
            $exception->getMessage()
        );
    }

    /** @test */
    public function it_handles_missing_response_fields(): void
    {
        $response = [(object) []];

        $exception = CouldNotSendNotification::emailWasRejected($response);

        $this->assertEquals(
            "Mailchimp responded with status 'unknown' for 'unknown': unknown",
            $exception->getMessage()
        );
    }

    /** @test */
    public function it_handles_partial_response_fields(): void
    {
        $response = [
            (object) [
                'status' => 'rejected',
            ],
        ];

        $exception = CouldNotSendNotification::emailWasRejected($response);

        $this->assertEquals(
            "Mailchimp responded with status 'rejected' for 'unknown': unknown",
            $exception->getMessage()
        );
    }

    /** @test */
    public function it_is_throwable(): void
    {
        $response = [
            (object) [
                'email' => 'test@example.com',
                'status' => 'rejected',
                'reject_reason' => 'invalid-sender',
            ],
        ];

        $this->expectException(CouldNotSendNotification::class);

        throw CouldNotSendNotification::emailWasRejected($response);
    }
}
