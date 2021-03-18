<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

    /**
     * Check that the signature provided is correct
     *
     * @return array
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $name = $this->input('name');
            $pubkey = $this->input('pubkey');
            $signature = explode(':', $this->input('signature'));

            if(empty($signature[0]) || $signature[0] != $name) {
                $validator->errors()->add('signature', 'The signature does not match the provided username.');
            }

            if(empty($signature[1]) || $signature[1] != $pubkey) {
                $validator->errors()->add('signature', 'Unable to verify the provided pubkey.');
            }
        });
    }
}
