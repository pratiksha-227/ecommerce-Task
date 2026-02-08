<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN API
    |--------------------------------------------------------------------------
    */
    public function login(Request $request): JsonResponse
    {
        try {

            $request->validate([
                'email' => 'required_without:email_or_phone|nullable|email',
                'email_or_phone' => 'required_without:email|nullable|string',
                'password' => 'required|string',
            ]);

            $cred = trim($request->input('email') ?? $request->input('email_or_phone', ''));

            // Normalize phone
            if (!filter_var($cred, FILTER_VALIDATE_EMAIL)) {
                $cred = preg_replace('/\D/', '', $cred);
            }

            $query = User::query();

            if (filter_var($cred, FILTER_VALIDATE_EMAIL)) {
                $query->where('email', $cred);
            } else {
                $query->where('phone', $cred);
            }

            $user = $query->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email/phone or password.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_admin' => (bool) $user->is_admin,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Login failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER API
    |--------------------------------------------------------------------------
    */
    public function register(Request $request): JsonResponse
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required_without:email_or_phone|nullable|email|unique:users,email',
                'email_or_phone' => 'required_without:email|nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
                'is_admin' => 'nullable|boolean',
            ]);

            $cred = trim($request->input('email') ?? $request->input('email_or_phone', ''));

            $isEmail = filter_var($cred, FILTER_VALIDATE_EMAIL);

            // Normalize phone
            if (!$isEmail) {
                $cred = preg_replace('/\D/', '', $cred);
            }

            $isAdmin = (bool) $request->input('is_admin', false);

            if ($isEmail) {

                $user = User::create([
                    'name' => trim($request->name),
                    'email' => $cred,
                    'phone' => null,
                    'password' => Hash::make($request->password),
                    'is_admin' => $isAdmin,
                ]);

            } else {

                $user = User::create([
                    'name' => trim($request->name),
                    'email' => null,
                    'phone' => $cred,
                    'password' => Hash::make($request->password),
                    'is_admin' => $isAdmin,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Registered successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_admin' => (bool) $user->is_admin,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Registration failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
