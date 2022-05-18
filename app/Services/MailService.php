<?php

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class MailService
{

    protected $api_key;
    protected $api_secret;

    public function __construct($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    public function send($from_email = 'rucred@ucase.live', $to_email = 'vs@zorca.org', $subject = 'Test Email', $text = 'Test Text', $text_html = '<h1>Test Text</h1>')
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => (string)$from_email,
                        'Name' => "RuCred"
                    ],
                    'To' => [
                        [
                            'Email' => (string)$to_email,
                            'Name' => "You"
                        ]
                    ],
                    'Subject' => $subject,
                    'TextPart' => $text,
                    'HTMLPart' => $text_html
                ]
            ]
        ];


        $mailjet = new Client($this->api_key, $this->api_secret,true, ['version' => 'v3.1']);
        $response = $mailjet->post(Resources::$Email, ['body' => $body]);
        //$response->success() && var_dump($response->getData());
        return $response;
    }

}
