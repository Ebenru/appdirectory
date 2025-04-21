<?php

namespace App\Http\Controllers;

use App\Models\Group; // Import Group
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of all approved groups.
     */
    public function index(): View
    {
        // Admins might see pending/draft, others only approved
        $query = Group::query();
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('status', 'approved'); // Only approved for public
        }

        $groups = $query->orderBy('name')
            // Optionally add withCount for members if needed on index
            // ->withCount('companies') // Counts companies linked via FK
            ->paginate(12);

        return view('groups.index', ['groups' => $groups]);
    }

    /**
     * Display the specified group and its related approved entities.
     */
    public function show(Group $group): View // Use route model binding
    {
        // Authorize viewing using a policy if created later
        // $this->authorize('view', $group);
        // For now, basic status check for non-admins
        if ($group->status !== 'approved' && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }


        // Eager load relationships with approved status filter for members
        $group->load([
            'companies' => function ($query) { // Assuming 'companies' is the HasMany relationship
                $query->where('status', 'approved')->orderBy('legalName');
            },
            'country', // Load the group's country
            'tags', // Load tags associated with the group
            'likes', // Load likes for the group itself
            // Eager load comments and their nested relationships for the group
            'comments' => function ($query) {
                $query->with(['user', 'likes', 'replies' => function ($subQuery) {
                    $subQuery->with(['user', 'likes'])->orderByDesc('created_at'); // Load replies recursively if needed
                }])->withCount('likes')->orderByDesc('created_at'); // Order top-level comments
            }
            // Add 'members' polymorphic relationship later if needed
        ]);

        // Pass comments separately for clarity in the view if needed
        $comments = $group->comments; // Access loaded relationship

        return view('groups.show', [
            'group' => $group,
            'comments' => $comments, // Pass sorted comments
            'currentSort' => request('sort', 'newest') // Pass sort default/param
        ]);
    }

    // Add create/store/edit/update/destroy later for admin management
}
