<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class SecretRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'secret' => 'required|string',
            'expireAfter' => 'required|integer|min:0',
            'expireAfterViews' => 'required|integer|min:1'
        ];
    }

    /* /**
      * @throws \Exception
      */
    public function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json('Invalid input', 405);

        throw new HttpResponseException($response);
    }
}
