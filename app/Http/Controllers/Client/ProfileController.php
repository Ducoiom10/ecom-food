<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Http\Requests\Client\ChangePasswordRequest;
use App\Models\Loyalty\LoyaltyChallenge;
use App\Models\Loyalty\UserChallengeProgress;
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

        return view('client.profile.index', [
            'user'         => $user,
            'snackPoints'  => $user->snack_points,
            'orderHistory' => $user->orders()->with('items.product')->latest()->take(10)->get(),
            'challenges'   => $challenges,
            'progressMap'  => $progressMap,
        ]);
    }

    public function edit()
    {
        return view('client.profile.edit', ['user' => auth()->user()]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $user->update($request->only('name', 'email'));

        return redirect()->route('client.profile')->with('success', 'Cập nhật thành công.');
    }

    public function showChangePassword()
    {
        return view('client.profile.change-password');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
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
