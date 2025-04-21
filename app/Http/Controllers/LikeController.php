<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Person; // Import models
use App\Models\Company;
use App\Models\Comment; // Import Comment model
use App\Models\Event;
use App\Models\Group;
use App\Models\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException; // For better error handling

class LikeController extends Controller
{
    /**
     * Toggle liking a specific item (Person or Company).
     */
    public function toggle(Request $request, string $type, int $id): RedirectResponse
    {
        // Determine the model class based on the type string
        $modelClass = match (strtolower($type)) {
            'person' => Person::class,
            'company' => Company::class,
            'comment' => Comment::class, // Add comment type
            'group' => Group::class,
            'organization' => Organization::class,
            'event' => Event::class,
            default => null,
        };

        if (!$modelClass) {
            // Use session flash for error messages
            return back()->with('error', 'Invalid item type specified.');
        }

        // Find the item or fail
        try {
            // Ensure you only interact with approved items if that's a requirement
            // Note: Depending on your app logic, you might allow viewing unapproved items but not liking? Adjust if needed.
            $likeable = $modelClass::findOrFail($id);

            // Optional: Add an explicit check if only approved items can be liked
            if (property_exists($likeable, 'status') && $likeable->status !== 'approved') {
                return back()->with('error', 'This item cannot be liked currently.');
            }
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Item not found.');
        }

        $user = Auth::user(); // Get the authenticated user

        // --- START: Corrected Like/Unlike Logic ---

        // Check if the user has already liked this specific item
        $existingLike = $likeable->likes() // Access the MorphMany relationship query builder
            ->where('user_id', $user->id) // Filter by the current user's ID
            ->first(); // Attempt to retrieve the first matching Like model or null

        $message = '';
        $liked = false; // Keep track of the action

        if ($existingLike) {
            // Like exists, so delete it (unlike)
            $existingLike->delete();
            $message = 'You unliked ' . ($likeable->displayName ?? $likeable->fullName ?? $likeable->legalName) . '.';
            $liked = false;
        } else {
            // Like does not exist, so create it
            $likeable->likes()->create([
                'user_id' => $user->id,
                // Add any other columns for the Like model if needed (e.g., timestamps are usually automatic)
            ]);
            $message = 'You liked ' . ($likeable->displayName ?? $likeable->fullName ?? $likeable->legalName) . '.';
            $liked = true;
        }

        // --- END: Corrected Like/Unlike Logic ---


        // Optionally return JSON if it's an AJAX request
        if ($request->expectsJson()) {
            return new RedirectResponse(response()->json([
                'message' => $message,
                'liked' => $liked,
                'like_count' => $likeable->likes()->count() // Return updated count
            ])->getContent());
        }

        // Redirect back to the previous page with a success message
        return back()->with('success', $message);
    }
} // Missing closing brace for the class added