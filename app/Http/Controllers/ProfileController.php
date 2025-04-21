<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Import Storage
use Illuminate\Validation\Rule; // Import Rule
use Illuminate\Validation\Rules\File; // Import File validation rule
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // --- Validate Base Fields (Name, Email - using ProfileUpdateRequest logic) ---
        $validatedBase = $request->validate([
             'name' => ['required', 'string', 'max:255'],
             'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], // Or rely on ProfileUpdateRequest
        ]);

        $user->fill($validatedBase);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null; // Reset verification if email changes
        }

        // --- Validate and Handle Bio & Photo ---
        $validatedExtra = $request->validate([
            'bio' => ['nullable', 'string', 'max:1000'], // Max 1000 chars for bio
            'photo' => [
                'nullable',
                File::image() // Use File rule for validation
                    ->max(2048) // Max 2MB (2048 KB)
                    // ->dimensions(Rule::dimensions()->maxWidth(1000)->maxHeight(1000)), // Optional dimensions
            ],
        ]);

        // Update Bio
        $user->bio = $validatedExtra['bio'] ?? null;

        // Handle Photo Upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // Delete old photo if it exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            // Store new photo (e.g., in 'profile-photos' directory)
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        // --- Save User ---
        $user->save(); // Save all changes

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ... (Keep existing destroy logic from Breeze) ...
        $request->validateWithBag('userDeletion', [
           'password' => ['required', 'current_password'],
       ]);

       $user = $request->user();

       Auth::logout();

        // Optional: Delete profile photo file when user is deleted
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

       $user->delete();

       $request->session()->invalidate();
       $request->session()->regenerateToken();

       return Redirect::to('/');
   }

}
