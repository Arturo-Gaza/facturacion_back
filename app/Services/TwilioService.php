<?php

namespace App\Services;

use Twilio\Rest\Client;
use App\Models\PasswordConfPhone;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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

    public function sendSMSConf($to)
    {
        try {
            $codigo= str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $message="El codigo de verificación es ".$codigo;

            PasswordConfPhone::create([
                'phone' => $to,
                'codigo' =>  Hash::make($codigo),
                'created_at' => Carbon::now(),
            ]);

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