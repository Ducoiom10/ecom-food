<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => 'Số điện thoại hoặc mật khẩu không đúng.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'phone' => 'Tài khoản đã bị khóa.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return $user->isAdmin()
            ? redirect()->route('admin.kds')
            : redirect()->route('client.home');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|max:15|unique:users,phone',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'customer',
        ]);

        Auth::login($user);

        return redirect()->route('client.home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        // TODO: gửi OTP qua SMS
        return back()->with('sent', true);
    }
}
