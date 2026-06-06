<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load(['roles', 'employee.department']);

        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Update basic info
            $user->name = $request->name;
            $user->email = $request->email;

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                // Store new profile picture
                $path = $request->file('profile_picture')->store('profile-pictures', 'public');
                $user->profile_picture = $path;
            }

            $user->save();

            return redirect()->route('profile.edit')->with('success', 'تم تحديث الملف الشخصي بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')->with('error', 'حدث خطأ أثناء تحديث الملف الشخصي');
        }
    }

    public function deleteProfilePicture()
    {
        $user = Auth::user();
        
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->profile_picture = null;
            $user->save();
        }

        return redirect()->route('profile.edit')->with('success', 'تم حذف صورة الملف الشخصي بنجاح');
    }
}