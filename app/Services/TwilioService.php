<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
        $this->from = env('TWILIO_PHONE_NUMBER');
    }

    public function sendSMS($to, $message)
    {
        try {
            $response = $this->client->messages->create(
                $to, // Número destino
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );

            return [
                'success' => true,
                'message_id' => $response->sid
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}