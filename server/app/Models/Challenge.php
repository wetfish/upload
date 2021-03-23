<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpseclib3\Crypt\PublicKeyLoader;
use App\Models\Key;

class Challenge extends Model
{
    // Challenges belong to a specific key
    public function key()
    {
        return $this->belongsTo(Key::class);
    }

    public function isValid($encodedSignature)
    {
        $pubkey = PublicKeyLoader::load($this->key()->pubkey);
        $signature = base64_decode($encodedSignature);

        if($pubkey->verify($this->string, $signature)) {
            return true;
        }

        return false;
    }
}
