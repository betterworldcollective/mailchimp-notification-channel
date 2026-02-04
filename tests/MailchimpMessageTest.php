<?php

namespace NotificationChannels\Mailchimp\Test;

use NotificationChannels\Mailchimp\MailchimpMessage;
use PHPUnit\Framework\TestCase;

class MailchimpMessageTest extends TestCase
{
    private MailchimpMessage $message;

    protected function setUp(): void
    {
        parent::setUp();
        $this->message = new MailchimpMessage();
    }

    /** @test */
    public function it_can_set_template_name(): void
    {
        $result = $this->message->templateName('welcome-email');

        $this->assertInstanceOf(MailchimpMessage::class, $result);
        $this->assertEquals('welcome-email', $this->message->getTemplateName());
    }

    /** @test */
    public function it_can_set_subject(): void
    {
        $result = $this->message->subject('Welcome to our app!');

        $this->assertInstanceOf(MailchimpMessage::class, $result);
        $this->assertEquals('Welcome to our app!', $this->message->getSubject());
    }

    /** @test */
    public function it_can_set_recipient(): void
    {
        $result = $this->message->to('john@example.com', 'John Doe');

        $this->assertInstanceOf(MailchimpMessage::class, $result);
        $this->assertEquals('john@example.com', $this->message->getTo()->getEmail());
        $this->assertEquals('John Doe', $this->message->getTo()->getName());
    }

    /** @test */
    public function it_can_set_sender(): void
    {
        $result = $this->message->from('sender@example.com', 'My App');

        $this->assertInstanceOf(MailchimpMessage::class, $result);
        $this->assertEquals('sender@example.com', $this->message->getFrom()->getFromEmail());
        $this->assertEquals('My App', $this->message->getFrom()->getFromName());
    }

    /** @test */
    public function it_can_set_sender_without_name(): void
    {
        $result = $this->message->from('sender@example.com');

        $this->assertInstanceOf(MailchimpMessage::class, $result);
        $this->assertEquals('sender@example.com', $this->message->getFrom()->getFromEmail());
        $this->assertNull($this->message->getFrom()->getFromName());
    }

    /** @test */
    public function it_can_add_merge_tags(): void
    {
        $result = $this->message
            ->addMergeTag('FIRST_NAME', 'John')
            ->addMergeTag('COMPANY', 'Acme Inc');

        $this->assertInstanceOf(MailchimpMessage::class, $result);
        $this->assertEquals(['FIRST_NAME' => 'John', 'COMPANY' => 'Acme Inc'], $this->message->getMergeTags());
    }

    /** @test */
    public function it_can_clear_merge_tags(): void
    {
        $this->message
            ->addMergeTag('FIRST_NAME', 'John')
            ->clearMergeTags();

        $this->assertEmpty($this->message->getMergeTags());
    }

    /** @test */
    public function it_can_enable_handlebars(): void
    {
        $this->message
            ->templateName('test-template')
            ->subject('Test')
            ->to('test@example.com', 'Test')
            ->from('sender@example.com', 'Sender')
            ->useHandleBars();

        $body = $this->message->getMessageBody();

        $this->assertEquals('handlebars', $body['message']['merge_language']);
    }

    /** @test */
    public function it_can_disable_handlebars(): void
    {
        $this->message
            ->templateName('test-template')
            ->subject('Test')
            ->to('test@example.com', 'Test')
            ->from('sender@example.com', 'Sender')
            ->useHandleBars(false);

        $body = $this->message->getMessageBody();

        $this->assertArrayNotHasKey('merge_language', $body['message']);
    }

    /** @test */
    public function it_builds_correct_message_body(): void
    {
        $this->message
            ->templateName('welcome-template')
            ->subject('Welcome!')
            ->to('john@example.com', 'John Doe')
            ->from('hello@myapp.com', 'My App')
            ->addMergeTag('FIRST_NAME', 'John')
            ->addMergeTag('ACTIVATION_URL', 'https://example.com/activate')
            ->useHandleBars();

        $body = $this->message->getMessageBody();

        $this->assertEquals('welcome-template', $body['template_name']);
        $this->assertEquals([['name' => '', 'content' => '']], $body['template_content']);

        $message = $body['message'];
        $this->assertEquals([['email' => 'john@example.com', 'name' => 'John Doe', 'type' => 'to']], $message['to']);
        $this->assertEquals('Welcome!', $message['subject']);
        $this->assertEquals('hello@myapp.com', $message['from_email']);
        $this->assertEquals('My App', $message['from_name']);
        $this->assertEquals('handlebars', $message['merge_language']);

        $this->assertCount(1, $message['merge_vars']);
        $this->assertEquals('john@example.com', $message['merge_vars'][0]['rcpt']);
        $this->assertCount(2, $message['merge_vars'][0]['vars']);
    }

    /** @test */
    public function it_builds_message_body_without_merge_vars_when_empty(): void
    {
        $this->message
            ->templateName('simple-template')
            ->subject('Hello')
            ->to('john@example.com', 'John')
            ->from('sender@example.com', 'Sender');

        $body = $this->message->getMessageBody();

        $this->assertArrayNotHasKey('merge_vars', $body['message']);
    }

    /** @test */
    public function it_builds_correct_recipient_vars(): void
    {
        $this->message
            ->to('john@example.com', 'John')
            ->addMergeTag('VAR1', 'value1')
            ->addMergeTag('VAR2', 'value2');

        $recipientVars = $this->message->getRecipientVars();

        $this->assertEquals('john@example.com', $recipientVars['rcpt']);
        $this->assertCount(2, $recipientVars['vars']);
        $this->assertEquals(['name' => 'VAR1', 'content' => 'value1'], $recipientVars['vars'][0]);
        $this->assertEquals(['name' => 'VAR2', 'content' => 'value2'], $recipientVars['vars'][1]);
    }

    /** @test */
    public function it_supports_fluent_chaining(): void
    {
        $result = $this->message
            ->templateName('template')
            ->subject('Subject')
            ->to('to@example.com', 'To')
            ->from('from@example.com', 'From')
            ->addMergeTag('KEY', 'value')
            ->useHandleBars();

        $this->assertInstanceOf(MailchimpMessage::class, $result);
    }
}
