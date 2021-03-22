<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class TestChallenge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:challenge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command for testing the creation of pubkeys and API challenges';

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
     * @return void
     */
    public function handle()
    {
        $privateKey = RSA::createKey(4096);
        $publicKey = $privateKey->getPublicKey();

        $payload = [
            'pubkey' => $publicKey->toString('PSS'),
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

        echo "Got response from API: " . $response;
    }
}
