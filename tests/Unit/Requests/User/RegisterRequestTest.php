<?php

namespace Tests\Unit\Requests\User;

use App\Http\Requests\User\RegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_request_with_valid_data()
    {
        $data = [
            'name' => 'Amir',
            'family' => 'Tahmasbii',
            'email' => 'amir@example.com',
            'password' => 'Amir1234!',
            'password_confirmation' => 'Amir1234!',
        ];

        $request = new RegisterRequest();
        $request->merge($data);

        $this->assertTrue($request->authorize());

        $this->assertEquals($request->rules(), [
            'name' => 'required|min:3|string',
            'family' => 'required|min:3|string',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'email' => 'required|email|unique:users,email',
        ]);
    }

    public function test_register_request_fails_due_to_missing_data()
    {
        $data = [];
        // Perform validation
        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('family', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertArrayHasKey('password_confirmation', $validator->errors()->toArray());
    }

    public function test_register_request_fails_due_to_invalid_password()
    {
        $data = [
            'name' => 'Amir',
            'family' => 'Tahmasbii',
            'email' => 'amir@example.com',
            'password' => 'password', // Weak password
            'password_confirmation' => 'password',
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_fails_due_to_duplicate_email()
    {
        User::create([
            'name' => 'Amir',
            'family' => 'Tahmasbii',
            'email' => 'amir@example.com',
            'password' => Hash::make('Amir1234!'),
        ]);

        $data = [
            'name' => 'Amir',
            'family' => 'Tahmasbii',
            'email' => 'amir@example.com', // Duplicate email
            'password' => 'Amir1234!',
            'password_confirmation' => 'Amir1234!',
        ];

        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
}

