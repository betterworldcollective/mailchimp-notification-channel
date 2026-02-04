<?php

namespace NotificationChannels\Mailchimp;

use Illuminate\Support\Arr;
use NotificationChannels\Mailchimp\Data\Recipient;
use NotificationChannels\Mailchimp\Data\Sender;

class MailchimpMessage
{
    private string $templateName;
    private string $fromEmail;
    private string $message;
    private array $mergeTags = [];
    private Recipient $to;
    private Sender $from;
    private bool $useHandleBars = false;

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function useHandleBars(bool $useHandleBars = true): MailchimpMessage
    {
       $this->useHandleBars = $useHandleBars;
       return $this;
    }

    public function templateName(string $templateName): MailchimpMessage
    {
        $this->templateName = $templateName;
        return $this;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function fromEmail(string $fromEmail): MailchimpMessage
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function message(string $message): MailchimpMessage
    {
        $this->message = $message;
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

    public function addMergeTags(string $name, string $content): MailchimpMessage
    {
        $this->mergeTags[] = ['name'=> $name, 'content'=>$content];

        return $this;
    }



    public function getMessageBody(): array
    {
        $message = [
            'to' => [ 'email'=> $this->getTo()],
            'from_email' => $this->getFromEmail(),
            'from_name' => $this->getFromName(),
            'merge_vars' => [$this->getRecipientVars()]
        ];

        if($this->useHandleBars){
            $message['merge_language']= 'handlebars';
        }

        return [
            'template_name' => $this->getTemplateName(),
            'template_content' => [['name' => '', 'content' => '']],
            'message' => $message
        ];
    }


    public function getRecipientVars(): array
    {
        $vars = array_map(fn($key, $mergeTag):array =>
                    ['name'=>$key, 'content'=>$mergeTag],
            array_keys($this->getMergeTags()), $this->getMergeTags());

       return [
           'rcpt'=>$this->getTo(),
           'vars'=> $vars
       ];
    }

    /**
     * @return \NotificationChannels\Mailchimp\Data\Recipient
     */
    public function getTo(): Recipient
    {
        return $this->to;
    }

    public function to(string $email, ?string $name): MailchimpMessage
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

    /**
     * @param  string  $email
     * @param  string|null  $name
     */
    public function from( string $email, ?string $name): void
    {
        $this->from = new Sender($email, $name);
    }
}
