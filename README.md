# Mailchimp Transactional Notifications Channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/betterworld/mailchimp-notification-channel.svg?style=flat-square)](https://packagist.org/packages/betterworld/mailchimp-notification-channel)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/betterworld/mailchimp-notification-channel.svg?style=flat-square)](https://packagist.org/packages/betterworld/mailchimp-notification-channel)

This package makes it easy to send notifications using [Mailchimp Transactional](https://mailchimp.com/developer/transactional/) (formerly Mandrill) with Laravel 10.x, 11.x, and 12.x.

## Contents

- [Installation](#installation)
    - [Setting up the Mailchimp Transactional service](#setting-up-the-mailchimp-transactional-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install this package via composer:

```bash
composer require betterworld/mailchimp-notification-channel
```

### Setting up the Mailchimp Transactional service

Add your Mailchimp Transactional API key and default sender information to your `.env` file:

```env
MAILCHIMP_TRANSACTIONAL_API_KEY=your-api-key
MAILCHIMP_FROM_NAME="Your App Name"
MAILCHIMP_FROM_EMAIL=noreply@yourapp.com
```

Optionally, you can publish the configuration file:

```bash
php artisan vendor:publish --provider="NotificationChannels\Mailchimp\MailchimpServiceProvider"
```

This will create a `config/mailchimp-notification-channel.php` file with the following options:

```php
return [
    'api_key' => env('MAILCHIMP_TRANSACTIONAL_API_KEY'),
    'from_name' => env('MAILCHIMP_FROM_NAME'),
    'from_email' => env('MAILCHIMP_FROM_EMAIL'),
];
```

## Usage

You can use the channel in your `via()` method inside a notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\Mailchimp\MailchimpChannel;
use NotificationChannels\Mailchimp\MailchimpMessage;

class AccountApproved extends Notification
{
    public function via($notifiable): array
    {
        return [MailchimpChannel::class];
    }

    public function toMailchimp($notifiable): MailchimpMessage
    {
        return (new MailchimpMessage())
            ->templateName('account-approved')
            ->subject('Your account has been approved!')
            ->to($notifiable->email, $notifiable->name)
            ->addMergeTag('FIRST_NAME', $notifiable->first_name)
            ->addMergeTag('LOGIN_URL', 'https://yourapp.com/login');
    }
}
```

### Routing Notifications

You can either pass the recipient directly in the message using the `to()` method, or implement `routeNotificationForMailchimp()` on your notifiable model:

```php
class User extends Authenticatable
{
    use Notifiable;

    public function routeNotificationForMailchimp($notification): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}
```

### Using Handlebars Merge Language

If your Mailchimp template uses Handlebars syntax, enable it on the message:

```php
public function toMailchimp($notifiable): MailchimpMessage
{
    return (new MailchimpMessage())
        ->templateName('welcome-email')
        ->subject('Welcome!')
        ->to($notifiable->email, $notifiable->name)
        ->useHandleBars()
        ->addMergeTag('firstName', $notifiable->first_name);
}
```

### Customizing the Sender

You can override the default sender on a per-notification basis:

```php
public function toMailchimp($notifiable): MailchimpMessage
{
    return (new MailchimpMessage())
        ->templateName('invoice')
        ->subject('Your Invoice')
        ->to($notifiable->email, $notifiable->name)
        ->from('billing@yourapp.com', 'Billing Department');
}
```

### Available Message methods

| Method | Description |
|--------|-------------|
| `templateName(string $templateName)` | Set the Mailchimp template name to use |
| `subject(string $subject)` | Set the email subject line |
| `to(string $email, ?string $name = null)` | Set the recipient email and optional name |
| `from(string $email, ?string $name = null)` | Override the default sender email and name |
| `addMergeTag(string $name, string $content)` | Add a merge tag variable for the template |
| `clearMergeTags()` | Clear all previously set merge tags |
| `useHandleBars(bool $useHandleBars = true)` | Enable Handlebars merge language for the template |

### Handling Errors

When a notification fails to send, a `NotificationFailed` event is dispatched. You can listen for this event to handle failures:

```php
use Illuminate\Notifications\Events\NotificationFailed;

Event::listen(NotificationFailed::class, function ($event) {
    // Handle the failed notification
    Log::error('Mailchimp notification failed', [
        'notifiable' => $event->notifiable,
        'notification' => $event->notification,
    ]);
});
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please email kristherlouie.vidal@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Kristher Louis Vidal](https://github.com/kristher1619)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
