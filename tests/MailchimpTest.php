<?php

namespace NotificationChannels\Mailchimp\Test;

use MailchimpTransactional\ApiClient;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use NotificationChannels\Mailchimp\Mailchimp;
use NotificationChannels\Mailchimp\MailchimpMessage;
use PHPUnit\Framework\TestCase;

class MailchimpTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function it_sends_message_using_mailchimp_client(): void
    {
        $expectedBody = [
            'template_name' => 'test-template',
            'template_content' => [['name' => '', 'content' => '']],
            'message' => [
                'to' => [['email' => 'john@example.com', 'name' => 'John', 'type' => 'to']],
                'subject' => 'Test Subject',
                'from_email' => 'sender@example.com',
                'from_name' => 'Sender',
            ],
        ];

        $expectedResponse = [
            (object) [
                'email' => 'john@example.com',
                'status' => 'sent',
                '_id' => 'abc123',
            ],
        ];

        $messages = Mockery::mock();
        $messages->shouldReceive('sendTemplate')
            ->once()
            ->with($expectedBody)
            ->andReturn($expectedResponse);

        $client = Mockery::mock(ApiClient::class);
        $client->messages = $messages;

        $mailchimp = new Mailchimp($client);

        $message = (new MailchimpMessage())
            ->templateName('test-template')
            ->subject('Test Subject')
            ->to('john@example.com', 'John')
            ->from('sender@example.com', 'Sender');

        $response = $mailchimp->sendMessage($message);

        $this->assertEquals($expectedResponse, $response);
    }

    /** @test */
    public function it_sends_message_with_merge_vars(): void
    {
        $messages = Mockery::mock();
        $messages->shouldReceive('sendTemplate')
            ->once()
            ->with(Mockery::on(function ($body) {
                return isset($body['message']['merge_vars'])
                    && $body['message']['merge_vars'][0]['rcpt'] === 'john@example.com'
                    && count($body['message']['merge_vars'][0]['vars']) === 2;
            }))
            ->andReturn([(object) ['status' => 'sent']]);

        $client = Mockery::mock(ApiClient::class);
        $client->messages = $messages;

        $mailchimp = new Mailchimp($client);

        $message = (new MailchimpMessage())
            ->templateName('test-template')
            ->subject('Test Subject')
            ->to('john@example.com', 'John')
            ->from('sender@example.com', 'Sender')
            ->addMergeTag('FIRST_NAME', 'John')
            ->addMergeTag('LAST_NAME', 'Doe');

        $mailchimp->sendMessage($message);
    }

    /** @test */
    public function it_sends_message_with_handlebars(): void
    {
        $messages = Mockery::mock();
        $messages->shouldReceive('sendTemplate')
            ->once()
            ->with(Mockery::on(function ($body) {
                return isset($body['message']['merge_language'])
                    && $body['message']['merge_language'] === 'handlebars';
            }))
            ->andReturn([(object) ['status' => 'sent']]);

        $client = Mockery::mock(ApiClient::class);
        $client->messages = $messages;

        $mailchimp = new Mailchimp($client);

        $message = (new MailchimpMessage())
            ->templateName('test-template')
            ->subject('Test Subject')
            ->to('john@example.com', 'John')
            ->from('sender@example.com', 'Sender')
            ->useHandleBars();

        $mailchimp->sendMessage($message);
    }
}
