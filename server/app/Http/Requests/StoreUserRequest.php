<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use phpseclib3\Crypt\PublicKeyLoader;
use App\Models\Challenge;

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
            'name' => 'required|string|unique:users',
            'email' => 'nullable|email',
            'description' => 'nullable|string',
            'challenge' => 'required|string',
            'signature' => 'required|string',
        ];
    }

    /**
     * Check that the signature provided is correct
     *
     * @return null
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if this challenge actually exists in the database
            $challenge = Challenge::where('string', $this->input('challenge'))->first();

            if(empty($challenge)) {
                $validator->errors()->add('challenge', 'This challenge does not exist in the database.');
            } elseif(!$challenge->isValid($this->input('signature'))) {
                $validator->errors()->add('signature', 'Unable to verify the signed challenge.');
            }
        });
    }
}
