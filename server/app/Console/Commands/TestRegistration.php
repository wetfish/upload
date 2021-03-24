<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class TestRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:registration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command for testing user registration by making cURL requests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function generateChallenge($pubkey)
    {
        $payload = [
            'pubkey' => $pubkey->toString('PSS'),
        ];

        // Set up all of our cURL options
        $request = curl_init(env('APP_URL') . '/api/v1/challenge');
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json',
        ));

        // Submit the POST request
        $response = curl_exec($request);
        curl_close($request);

        return json_decode($response);
    }

    /**
     * Execute the console command.
     *
     * Equivalent to running: curl -H "Accept: application/json" -H "Content-Type: application/json" -d '{"name": "rachel", "pubkey": "example", "signature": "rachel:signed data"}' -X POST http://upload.local/api/v1/user
     *
     * @return void
     */
    public function handle()
    {
        // Generate a keypair
        $privateKey = RSA::createKey(4096);
        $publicKey = $privateKey->getPublicKey();
        dump("Keypair generated.");

        // Generate a challenge
        $response = $this->generateChallenge($publicKey);
        dump("Challenge generated - {$response->challenge}");

        // Sign the challenge
        $signature = base64_encode($privateKey->sign($response->challenge));
        dump("Challenge signed - {$signature}");

        // Submit post request
        $payload = [
            'name' => "rachel",
            'challenge' => $response->challenge,
            'signature' => $signature,
        ];

        // Set up all of our cURL options
        $request = curl_init(env('APP_URL') . '/api/v1/user');
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/json',
        ));

        // Submit the POST request
        $result = curl_exec($request);
        curl_close($request);

        echo "Got result from API: " . $result;
    }
}
