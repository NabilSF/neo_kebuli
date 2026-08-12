<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private UserProfileService $profileService)
    {
    }

    public function index(Request $request)
    {
        $data = $this->profileService->getProfileData($request->user());

        return view('user.profile.index', compact('data'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->profileService->updateProfile($request->user(), $request->validated());

        return back()->with('status', 'profile-updated')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->profileService->updatePassword($request->user(), $request->validated('password'));

        return back()->with('status', 'password-updated')->with('success', 'Password berhasil diperbarui.');
    }
}
