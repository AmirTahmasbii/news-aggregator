<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\ForgetPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
/**
 * @group User 
 *
 * APIs for managing users
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * User Login
     * @bodyParam email string required Must be a valid email address. Example: hahn.bailey@example.org
     * @bodyParam password string required at least 8 characters, included 1 uppercase, 1 lowercase, symbols and numbers. Example: Amir1234!
     * @response 401 scenario="Email or Password is incorrect!" {"status": "error", "message": "Email or Password is incorrect!"}
     * @response 200 scenario="Email or Password is correct!" {"status": "success", "data": {"token": "1|ugfdshgfpoayhosgdh]"}}
     */
    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Email or Password is incorrect!'], 401);
        }

        if (!Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Email or Password is incorrect!'], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('bearer', ['*'], now()->addWeek());

        return response()->json(['status' => 'success', 'data' => ['token' => $token->plainTextToken]]);
    }

    /**
     * Register
     *
     * User Register
     * @bodyParam name string required min 3 chars. Example: Amir
     * @bodyParam family string required min 3 chars. Example: Amir
     * @bodyParam email string required Must be a valid email address. Example: hahn.bailey@example.org
     * @bodyParam password string required at least 8 characters, included 1 uppercase, 1 lowercase, symbols and numbers. Example: Amir1234!
     * @response 200 scenario="success" {"status": "success", "data": {"token": "1|ugfdshgfpoayhosgdh"}}
     */
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'family' => $validatedData['family'],
            'password' => Hash::make($validatedData['password']),
            'email' => $validatedData['email'],
        ]);

        $token = $user->createToken('bearer', ['*'], now()->addWeek());

        return response()->json(['status' => 'success', 'data' => ['token' => $token->plainTextToken]]);
    }

    /**
     * Logout
     *
     * User Logout
     *
     * @authenticated
     * 
     * @response 200 scenario="success" {"status": "success", "message": "logout was success!"}
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['status' => 'success', 'message' => 'logout was success!']);
    }

    /**
     * Send Reset Link Email
     *
     * @response 200 scenario="success" {"status": "success", "message": "Reset link sent to your email."}
     * @response 400 scenario="error" {"status": "error", "message": "Unable to send reset link."}
     */
    public function sendResetLinkEmail(ForgetPasswordRequest $request)
    {
        $validatedData = $request->validated();

        $response = Password::sendResetLink(['email' => $validatedData['email']]);

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['status' => 'success', 'message' => 'Reset link sent to your email.'], 200)
            : response()->json(['status' => 'error', 'message' => 'Unable to send reset link.'], 400);
    }

    /**
     * Reset Password
     *
     * @response 200 scenario="success" {"status": "success", "message": "Password has been reset."}
     * @response 400 scenario="error" {"status": "error", "message": "Failed to reset password."}
     */
    public function reset(ResetPasswordRequest $request)
    {
        $validatedData = $request->validated();

        $response = Password::reset(
            [
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'password_confirmation' => $validatedData['password_confirmation'],
                'token' => $_REQUEST['token']
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $response == Password::PASSWORD_RESET
            ? response()->json(['status' => 'success', 'message' => 'Password has been reset.'], 200)
            : response()->json(['status' => 'error', 'message' => 'Failed to reset password.'], 400);
    }
}
