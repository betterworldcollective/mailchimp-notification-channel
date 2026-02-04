<?php

return [
    /**
     * Mailchimp Transactional API Key or the Mandrill API Key
     * @see https://mailchimp.com/developer/transactional/docs/quick-start/
     */
    'api_key' => env('MAILCHIMP_TRANSACTIONAL_API_KEY'),

    'from_name' => env('MAILCHIMP_FROM_NAME'),

    'from_email' => env('MAILCHIMP_FROM_EMAIL'),
];

