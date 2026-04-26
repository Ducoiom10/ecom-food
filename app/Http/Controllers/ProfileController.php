<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('orders');

        return view('client.profile', [
            'user'         => $user,
            'snackPoints'  => $user->snack_points,
            'orderHistory' => $user->orders()
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function edit()
    {
        return view('auth.profile-edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        return redirect()->route('client.profile')->with('success', 'Cập nhật thành công.');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Mật khẩu hiện tại không đúng.',
            ]);
        }

        $user->update(['password' => $request->password]);

        return redirect()->route('client.profile')->with('success', 'Đổi mật khẩu thành công.');
    }
}
