<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use phpseclib3\Crypt\PublicKeyLoader;

class Pubkey implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $pubkey = PublicKeyLoader::load($value);
        } catch(\Throwable $exception) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid pubkey provided. Expected RSA-PSS format.';
    }
}
