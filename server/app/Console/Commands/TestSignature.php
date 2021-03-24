<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib3\Crypt\RSA;

class TestSignature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:signature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to test signing challenges';

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
        $keyfile = $this->ask('Enter the path to the private key file to be used');

        if(empty($keyfile)) {
            dd("Error! You must specify a keyfile");
        } else {
            $privateKey = RSA::loadFormat('PSS', file_get_contents($keyfile));
            $publicKey = $privateKey->getPublicKey();
            dump("Keypair loaded from {$keyfile}");
        }

        $challenge = $this->ask('Enter the challenge string to be signed');
        $signature = base64_encode($privateKey->sign($challenge));
        dump("Challenge signed - {$signature}");
    }
}
