<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            // Check role membership correctly (belongsToMany returns a collection)
            $isAdminSystem = $user->roles()->where('name', 'admin_system')->exists();
            return $isAdminSystem
                ? redirect()->intended(route('dashboard'))
                : redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show the authenticated user's profile page (single profile view).
     */
    public function profileShow()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update authenticated user's basic profile data (name, phone, email)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:100','unique:users,email,' . $user->id . ',id'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();
        $user = $user->fresh();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $user,
        ]);
    }

    /**
     * Update profile photo immediately on file change
     */
    public function updateProfilePhoto(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'photo' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
            ]);

            // Delete old file if exists
            if (!empty($user->profile_photo_path) && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Simpan ke folder 'avatar' (singular) agar sesuai dengan struktur di public/storage
            $path = $request->file('photo')->store('avatar', 'public');
            $user->profile_photo_path = $path; // store relative path like avatars/xxx.jpg
            $user->save();
            $user = $user->fresh();

            return response()->json([
                'message' => 'Foto profil berhasil diperbarui',
                'data' => [
                    'photo_url' => $user->photo_url,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to upload profile photo', [
                'user_id' => optional(Auth::user())->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Gagal upload foto',
                // Return as validation-like structure so UI can surface it under the photo field
                'errors' => [
                    'photo' => [config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan saat mengunggah foto.'],
                ],
            ], 500);
        }
    }

    /**
     * Change password with current password verification
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'old_password' => ['required','string'],
            'new_password' => ['required','string','min:8','confirmed'],
        ]);

        if (!Hash::check($validated['old_password'], $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => ['Password lama tidak sesuai.'],
            ]);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah',
        ]);
    }
}
