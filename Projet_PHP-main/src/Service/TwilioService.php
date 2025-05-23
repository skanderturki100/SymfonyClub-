<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private $accountSid;
    private $authToken;
    private $twilioPhoneNumber;

    public function __construct(string $accountSid, string $authToken, string $twilioPhoneNumber)
    {
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->twilioPhoneNumber = $twilioPhoneNumber;
    }

    public function sendSMS(string $to, string $body): void
    {
        $client = new Client($this->accountSid, $this->authToken);
        $client->messages->create($to, [
            'from' => $this->twilioPhoneNumber,
            'body' => $body,
        ]);
    }
}
