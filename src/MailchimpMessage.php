<?php

namespace NotificationChannels\Mailchimp;

use NotificationChannels\Mailchimp\Data\Recipient;
use NotificationChannels\Mailchimp\Data\Sender;

class MailchimpMessage
{
    private string $templateName;
    private ?string $subject = null;
    private array $mergeTags = [];
    private ?Recipient $to = null;
    private ?Sender $from = null;
    private bool $useHandleBars = false;

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function templateName(string $templateName): MailchimpMessage
    {
        $this->templateName = $templateName;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function subject(string $subject): MailchimpMessage
    {
        $this->subject = $subject;
        return $this;
    }

    public function useHandleBars(bool $useHandleBars = true): MailchimpMessage
    {
        $this->useHandleBars = $useHandleBars;
        return $this;
    }

    public function getMergeTags(): array
    {
        return $this->mergeTags;
    }

    public function clearMergeTags(): MailchimpMessage
    {
       $this->mergeTags = [];
       return $this;
    }

    public function addMergeTag(string $name, string $content): MailchimpMessage
    {
        $this->mergeTags[$name] = $content;

        return $this;
    }

    public function getMessageBody(): array
    {
        $message = [
            'to' => [$this->to->toArray()],
            'subject' => $this->getSubject(),
            ...$this->from?->toArray() ??[],
        ];

        if (!empty($this->mergeTags)) {
            $message['merge_vars'] = [$this->getRecipientVars()];
        }

        if ($this->useHandleBars) {
            $message['merge_language'] = 'handlebars';
        }

        return [
            'template_name' => $this->getTemplateName(),
            'template_content' => [['name' => '', 'content' => '']],
            'message' => $message,
        ];
    }

    public function getRecipientVars(): array
    {
        $vars = [];
        foreach ($this->mergeTags as $name => $content) {
            $vars[] = ['name' => $name, 'content' => $content];
        }

        return [
            'rcpt' => $this->to->getEmail(),
            'vars' => $vars,
        ];
    }

    /**
     * @return \NotificationChannels\Mailchimp\Data\Recipient
     */
    public function getTo(): ?Recipient
    {
        return $this->to;
    }

    public function to(string $email, ?string $name=null): MailchimpMessage
    {
        $this->to = new Recipient($email, $name);

        return $this;
    }

    /**
     * @return \NotificationChannels\Mailchimp\Data\Sender
     */
    public function getFrom(): Sender
    {
        return $this->from;
    }

    public function from(string $email, ?string $name = null): MailchimpMessage
    {
        $this->from = new Sender($email, $name);

        return $this;
    }
}
