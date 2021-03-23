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

        // Only validate a challenge if it hasn't been completed yet
        if(is_null($this->completed)) {
            if($pubkey->verify($this->string, $signature)) {
                $this->completed = true;
                $this->save();

                $this->key()->challenges_completed += 1;
                $this->key()->save();

                return true;
            } else {
                $this->completed = false;
                $this->save();
            }
        }

        return false;
    }
}
