<?php

namespace Tests\Unit\Requests\User;

use App\Http\Requests\User\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_request_validation()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123!'
        ];

        $request = new LoginRequest();
        $request->merge($data);

        $this->assertTrue($request->authorize());

        $this->assertEquals($request->rules(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    }

    public function test_failed_validation_response()
    {
        $data = [];
        // Perform validation
        $request = new LoginRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray(), 'The email field is required.');
        $this->assertArrayHasKey('password', $validator->errors()->toArray(), 'The password field is required.');
    }
}
