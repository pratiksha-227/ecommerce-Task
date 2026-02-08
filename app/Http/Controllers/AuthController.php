<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('products.index');
        }
        return redirect()->route('products.index')->with('open_signin_modal', true);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required',
        ], [
            'email_or_phone.required' => 'Enter your email or phone number.',
        ]);

        $cred = trim($request->input('email_or_phone'));
        $user = User::where('email', $cred)->orWhere('phone', $cred)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, (bool) $request->remember);
            $request->session()->regenerate();
            return redirect()->intended(route('products.index'))->with('success', 'Signed in successfully.');
        }

        return redirect()->route('products.index')
            ->withErrors(['email_or_phone' => 'Invalid email/phone or password. Create an account if you don\'t have one.'])
            ->withInput($request->only('email_or_phone'))
            ->with('open_signin_modal', true);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email_or_phone' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Enter your name.',
            'email_or_phone.required' => 'Enter your email or phone number.',
        ]);

        $cred = trim($request->input('email_or_phone'));
        $isEmail = str_contains($cred, '@');

        if ($isEmail) {
            $request->validate(['email_or_phone' => 'email'], ['email_or_phone.email' => 'Enter a valid email address.']);
            if (User::where('email', $cred)->exists()) {
                return redirect()->route('products.index')
                    ->withErrors(['email_or_phone' => 'This email is already registered. Sign in instead.'])
                    ->withInput($request->only('name', 'email_or_phone'))
                    ->with('open_signup_modal', true);
            }
            $user = User::create([
                'name' => trim($request->name),
                'email' => $cred,
                'phone' => null,
                'password' => Hash::make($request->password),
            ]);
        } else {
            $phone = preg_replace('/\D/', '', $cred);
            if (strlen($phone) < 10) {
                return redirect()->route('products.index')
                    ->withErrors(['email_or_phone' => 'Enter a valid phone number.'])
                    ->withInput($request->only('name', 'email_or_phone'))
                    ->with('open_signup_modal', true);
            }
            if (User::where('phone', $cred)->exists()) {
                return redirect()->route('products.index')
                    ->withErrors(['email_or_phone' => 'This phone number is already registered. Sign in instead.'])
                    ->withInput($request->only('name', 'email_or_phone'))
                    ->with('open_signup_modal', true);
            }
            $user = User::create([
                'name' => trim($request->name),
                'email' => null,
                'phone' => $cred,
                'password' => Hash::make($request->password),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('products.index')->with('success', 'Account created. You are signed in as ' . $user->name . '.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('products.index')->with('success', 'Logged out.');
    }
}
