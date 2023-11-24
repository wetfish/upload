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
        $keyfile = $this->ask('Enter the path to the private key file to be used, or leave this blank and one will be generated for you');

        if(empty($keyfile)) {
            // Generate a keypair
            $privateKey = RSA::createKey(4096);
            $publicKey = $privateKey->getPublicKey();
            dump("Keypair generated");
        } else {
            $privateKey = RSA::loadFormat('PKCS8', file_get_contents($keyfile));
            $publicKey = $privateKey->getPublicKey();
            dump("Keypair loaded from {$keyfile}");
        }

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
        $response = json_decode(curl_exec($request));
        $signature = base64_encode($privateKey->sign($response->challenge));
        curl_close($request);

        dump("challenge={$response->challenge}");
        dump("signature={$signature}");
    }
}
