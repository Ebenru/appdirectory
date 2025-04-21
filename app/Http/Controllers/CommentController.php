<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\Comment;
use App\Models\Person;
use App\Models\Company;
use App\Models\Event;
use App\Models\Group;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException; // Keep for findOrFail

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'commentable_type' => ['required', 'string', Rule::in(['person', 'company', 'group', 'Organization', 'event'])],
            'commentable_id' => ['required', 'integer'],
            'text' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);
        //dd($validated);
        // --- START: Verification - Ensure commentable exists ---
        $modelClass = match ($validated['commentable_type']) {
            'person' => Person::class,
            'company' => Company::class,
            'group' => Group::class,
            'organization' => Organization::class,
            'event' => Event::class,
            default => null,
        };

        try {
            // We still need to check if the main item exists before allowing comments/replies
            $commentableExists = $modelClass::where('id', $validated['commentable_id'])->exists();
            if (!$commentableExists) {
                throw new ModelNotFoundException(); // Trigger the catch block
            }
            // Optional: Add status check here if needed
            // $commentable = $modelClass::findOrFail($validated['commentable_id']);
            // if ($commentable->status !== 'approved') { ... }

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Cannot post comment to this item.');
        }
        // --- END: Verification ---


        // --- START: Create Comment Directly ---
        Comment::create([
            'commentable_id' => $validated['commentable_id'],       // ID of the Person/Company
            'commentable_type' => $validated['commentable_type'],   // Use the validated simple string ('person' or 'company')
            'parent_id' => $validated['parent_id'] ?? null,         // ID of the comment being replied to (if any)
            'user_id' => Auth::id(),                                // ID of the user posting
            'text' => $validated['text'],                           // The comment/reply text
        ]);
        // --- END: Create Comment Directly ---

        $message = isset($validated['parent_id']) ? 'Reply posted successfully!' : 'Comment posted successfully!';
        return back()->with('success', $message);
    }
}
