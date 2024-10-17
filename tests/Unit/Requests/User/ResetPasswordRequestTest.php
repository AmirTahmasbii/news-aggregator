<?php

namespace Tests\Unit\Requests\User;

use App\Http\Requests\User\ResetPasswordRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

class ResetPasswordRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_validates_correctly_when_provided_valid_data()
    {
        // Arrange
        $data = [
            'email' => 'test@example.com',
            'token' => 'valid-token',
            'password' => 'Valid123!',
            'password_confirmation' => 'Valid123!',
        ];

        // Act
        $request = new ResetPasswordRequest();
        $request->merge($data);

        $this->assertTrue($request->authorize());
        $this->assertEquals($request->rules(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);
    }


    /**
     * Test invalid data fails validation.
     */
    public function test_it_fails_validation_with_invalid_inputs()
    {
        $data = [
            'email' => 'invalid-email',
            'token' => '',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ];

        // Perform validation
        $request = new ResetPasswordRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('token', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertArrayHasKey('password_confirmation', $validator->errors()->toArray());
    }

    /**
     * Test missing password confirmation fails validation.
     */
    public function test_it_fails_validation_with_missing_password_confirmation()
    {
        $data = [
            'email' => 'user@example.com',
            'token' => 'valid-reset-token',
            'password' => 'ValidPassword1!',
        ];

        // Perform validation
        $request = new ResetPasswordRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password_confirmation', $validator->errors()->toArray());

    }
}
