<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyChallenge;
use App\Models\UserChallengeProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $challenges  = LoyaltyChallenge::where('is_active', true)->get();
        $progressMap = UserChallengeProgress::where('user_id', $user->id)
            ->get()->keyBy('challenge_id');

        return view('client.profile', [
            'user'         => $user,
            'snackPoints'  => $user->snack_points,
            'orderHistory' => $user->orders()->with('items.product')->latest()->take(10)->get(),
            'challenges'   => $challenges,
            'progressMap'  => $progressMap,
        ]);
    }

    public function edit()
    {
        return view('client.profile-edit', ['user' => auth()->user()]);
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
        return view('client.change-password');
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
