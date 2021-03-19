<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use phpseclib3\Crypt\PublicKeyLoader;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TOOD: Make sure the user is logged out?
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'nullable|email',
            'pubkey' => 'required|string',
            'description' => 'nullable|string',
            'signature' => 'required|string',
        ];
    }

    private function validSignature($pubkey, $payload, $signature)
    {
        // We have to JSON encode the payload so the array of input data is treated as a string when it gets signed
        $payload = json_encode($payload);

        // The signature is base64 encoded in transit, but the verify function expects raw bytes
        $signature = base64_decode($signature);

        if($pubkey->verify($payload, $signature)) {
            return true;
        }

        return false;
    }

    /**
     * Check that the signature provided is correct
     *
     * @return null
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // We have to remove the signature from the payload because the signature is generated based on the input data
            $payload = $this->input();
            unset($payload['signature']);

            $name = $this->input('name');
            $signature = explode(':', $this->input('signature'));

            try {
                $pubkey = PublicKeyLoader::load($this->input('pubkey'));
            } catch(\Throwable $exception) {
                $validator->errors()->add('pubkey', 'Invalid pubkey provided. Expected RSA-PSS format.');
                return;
            }

            if(empty($signature[0]) || $signature[0] != $name) {
                $validator->errors()->add('signature', 'The signature does not match the provided username.');
            }

            if(empty($signature[1]) || !$this->validSignature($pubkey, $payload, $signature[1])) {
                $validator->errors()->add('signature', 'Unable to verify the signed payload.');
            }
        });
    }
}
