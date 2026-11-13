<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $data = User::where('id', $userId)->first();
        $stats = Article::where('user_id', $userId)
                    ->selectRaw('
                        COUNT(*) as total_articles,
                        COUNT(CASE WHEN is_published = 1 THEN 1 END) as published_articles
                    ')
                    ->first();
        $articlesWritten = $stats->total_articles;
        $articlesPublished = $stats->published_articles;
        $totalArticles = $stats->total_articles;
        return view('profile.index', compact('data', 'articlesWritten', 'articlesPublished', 'totalArticles'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ], [
            'photo.required' => 'Please select a profile picture.',
            'photo.image' => 'The uploaded file must be an image.',
            'photo.mimes' => 'Only PNG, JPG, and JPEG files are allowed.',
            'photo.max' => 'The profile picture must not exceed 2 MB.',
        ]);

        $user = Auth::user();

        // Keep the old filename so we can delete it after a successful update.
        $oldPhoto = $user->photo;

        // Generate a unique filename and store the image.
        $filename = $request->file('photo')->hashName();

        $request->file('photo')->storeAs(
            'profile-pic',
            $filename,
            'public'
        );

        /** @var \App\Models\User $user */
        // Save only the filename in the database.
        $user->update([
            'photo' => $filename,
        ]);

        // Delete the previous image after the new one has been saved.
        if ($oldPhoto) {
            Storage::disk('public')->delete('profile-pic/' . $oldPhoto);
        }

        return redirect()->back()->with('alert', [
                    'type' => 'success',
                    'title' => 'Profile Changed!',
                    'message' => "Profile picture updated successfully.",
                ]);
    }


    public function updateInformation(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'name.required' => 'Please enter your full name.',
            'username.required' => 'Please enter a username.',
            'username.unique' => 'This username is already taken.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
        ]);

        // Check whether the email address has actually changed.
        $emailChanged = strcasecmp($user->email, $validated['email']) !== 0;

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        // A changed email address must be verified again.
        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        /** @var \App\Models\User $user */
        $user->save();

        return redirect()->back()
            ->with('alert', [
                    'type' => 'success',
                    'title' => 'Profile Updated!',
                    'message' => $emailChanged
                ? 'Your profile information has been updated. Please verify your new email address.'
                : 'Your profile information has been updated successfully.',
                ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([

            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

        ]);

        $request->user()->update([

            'password' => Hash::make($validated['password']),

        ]);

        return redirect()
            ->route('admin.profile.index')
            ->with('alert', [
                    'type' => 'success',
                    'title' => 'Password Changed!',
                    'message' => "Your password has been changed successfully.",
                ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deletePhoto()
    {
        $user = Auth::user();

        if (!$user->photo) {
            return redirect()->back()->with(
                'info',
                'You do not have a profile picture to delete.'
            );
        }

        $photoPath = 'profile-pic/' . basename($user->photo);

        /** @var \App\Models\User $user */
        // Remove the photo reference from the user account.
        $user->photo = null;
        $user->save();

        // Delete the actual image from public storage.
        Storage::disk('public')->delete($photoPath);

        return redirect()->back()
        ->with('alert', [
                    'type' => 'warning',
                    'title' => 'Profile Deleted!',
                    'message' => "Profile picture deleted successfully.",
                ]);
    }
}
