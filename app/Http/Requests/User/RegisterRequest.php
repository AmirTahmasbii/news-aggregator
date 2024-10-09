<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => 'required|min:3|string',
            'family' => 'required|min:3|string',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'email' => 'required|email|unique:App\Models\User,email',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $this->parseErrors($validator->errors()),
        ], 422));
    }

    protected function parseErrors($errors)
    {
        $parsedErrors = [];
        foreach ($errors->all() as $key => $error) {
            $parsedErrors[] = $error;
        }

        return $parsedErrors;
    }
}
