<?php

namespace Tests\Unit\Requests\User;

use App\Http\Requests\User\ForgetPasswordRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\TestCase;

class ForgetPasswordRequestTest extends TestCase
{
    public function test_login_request_validation()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123!'
        ];

        $request = new ForgetPasswordRequest();
        $request->merge($data);

        $this->assertTrue($request->authorize());

        $this->assertEquals($request->rules(), [
            'email' => 'required|email',
        ]);
    }

    public function test_failed_validation_response()
    {
        $data = [];
        // Perform validation
        $request = new ForgetPasswordRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray(), 'The email field is required.');
    }
}
