<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Laravel\Facades\Image;

class UserProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function show()
    {
        $user = auth()->user();
        
        // Eager load transactions ordered by latest
        $transactions = $user->transactions()
            ->latest()
            ->take(10)
            ->get();

        return view('profile.show', compact('user', 'transactions'));
    }

    /**
     * Update user profile settings.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Ad soyad alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanımda.',
        ]);

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil bilgileriniz başarıyla güncellendi.');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ], [
            'current_password.required' => 'Mevcut şifreniz zorunludur.',
            'current_password.current_password' => 'Mevcut şifreniz hatalı.',
            'password.required' => 'Yeni şifre alanı zorunludur.',
            'password.confirmed' => 'Şifre onaylaması eşleşmiyor.',
            'password.min' => 'Yeni şifreniz en az 8 karakter olmalıdır.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Şifreniz başarıyla değiştirildi.');
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => [
                'required',
                File::image()
                    ->types(['jpeg', 'jpg', 'png', 'webp'])
                    ->max('2mb'),
            ]
        ], [
            'avatar.required' => 'Bir resim seçmelisiniz.',
            'avatar.image' => 'Dosya bir resim olmalıdır.',
            'avatar.max' => 'Resim boyutu en fazla 2 MB olabilir.',
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            Storage::disk('public')->makeDirectory('avatars');

            // Read image and resize to 200x200
            $img = Image::read($avatarFile);
            $img->resize(200, 200);
            $encoded = $img->toWebp(80);

            // Generate unique filename
            $filename = Str::uuid()->toString() . '.webp';
            $filePath = 'avatars/' . $filename;

            // Save WebP image
            Storage::disk('public')->put($filePath, $encoded->toString());

            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Update user model with avatar path
            $user->update([
                'avatar' => $filePath
            ]);

            return redirect()->route('profile.show')->with('success', 'Profil resminiz başarıyla güncellendi.');
        }

        return redirect()->back()->with('error', 'Resim yüklenirken bir hata oluştu.');
    }

    /**
     * Deactivate user account (is_active = false).
     */
    public function deactivate(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => 'required|current_password',
        ], [
            'password.required' => 'Hesabınızı pasif etmek için şifrenizi girmeniz gerekmektedir.',
            'password.current_password' => 'Şifreniz hatalı.',
        ]);

        $user = auth()->user();

        // Mark user as inactive
        $user->update([
            'is_active' => false
        ]);

        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Hesabınız pasif hale getirilmiştir. Tekrar etkinleştirmek için lütfen bizimle iletişime geçin.');
    }
}
