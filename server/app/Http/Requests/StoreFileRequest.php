<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Challenge;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'file' => 'required',
            'file.1' => 'file|mimes:jpg,bmp,png,gif,webm,webp,mp3,mp4,wav,ogg,ogv',
            'file.2' => 'file|mimes:jpg,bmp,png,gif,webm,webp,mp3,mp4,wav,ogg,ogv',
            'file.3' => 'file|mimes:jpg,bmp,png,gif,webm,webp,mp3,mp4,wav,ogg,ogv',
            'file.4' => 'file|mimes:jpg,bmp,png,gif,webm,webp,mp3,mp4,wav,ogg,ogv',
            'file.5' => 'file|mimes:jpg,bmp,png,gif,webm,webp,mp3,mp4,wav,ogg,ogv',
            'gallery' => 'nullable|integer',
            'read_permission' => 'nullable|string',
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
