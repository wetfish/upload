<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
     * @return int
     */
    public function handle()
    {
        // curl -H "Accept: application/json" -H "Content-Type: application/json" -d '{"name": "rachel", "pubkey": "test", "signature": "rachel:test"}' -X POST http://upload.local/api/v1/user
        $payload = [
            'name' => 'rachel',
            'pubkey' => 'test',
            'signature' => 'rachel:test',
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

        echo $result;
    }
}
