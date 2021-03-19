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

    /**
     * Execute the console command.
     *
     * Equivalent to running: curl -H "Accept: application/json" -H "Content-Type: application/json" -d '{"name": "rachel", "pubkey": "example", "signature": "rachel:signed data"}' -X POST http://upload.local/api/v1/user
     *
     * @return int
     */
    public function handle()
    {
        $privateKey = RSA::createKey(4096);
        $publicKey = $privateKey->getPublicKey();
        $testUser = "rachel";

        $payload = [
            'name' => $testUser,
            'pubkey' => $publicKey->toString('PSS'),
        ];

        $signature = $privateKey->sign(json_encode($payload));
        $encodedSignature = $testUser . ":" . base64_encode($signature);

        $payload['signature'] = $encodedSignature;

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

        echo $result;
    }
}
