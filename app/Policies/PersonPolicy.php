<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PersonPolicy
{
    /**
     * Determine whether the user can view any models (used by index typically).
     * Allow anyone for now.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model (used by show typically).
     * Allow if approved, or if user is admin, or if user is submitter.
     */
    public function view(?User $user, Person $person): bool
    {
        if ($person->status === 'approved') {
            return true;
        }
        // Allow viewing own non-approved or if admin
        return $user && ($user->is_admin || $user->id === $person->submitted_by_id);
    }

    /**
     * Determine whether the user can create models.
     * Allow any logged-in user.
     */
    public function create(User $user): bool
    {
        return true; // Or check specific roles if needed later
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Person $person): bool
    {
        // Admin can always update
        if ($user->is_admin) {
            return true;
        }
        // User can update *only if* they submitted it AND it's still pending
        return $user->id === $person->submitted_by_id && $person->status === 'pending';

        // Logic for "Request Edit" on approved items would go here later
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Person $person): bool
    {
        // Admin can always delete
        if ($user->is_admin) {
            return true;
        }
        // User can delete *only if* they submitted it AND it's still pending
        return $user->id === $person->submitted_by_id && $person->status === 'pending';

        // Logic for "Request Delete" on approved items would go here later
    }

    /**
     * Determine whether the user can restore the model. (Optional - for soft deletes)
     */
    // public function restore(User $user, Person $person): bool { ... }

    /**
     * Determine whether the user can permanently delete the model. (Optional - for soft deletes)
     */
    // public function forceDelete(User $user, Person $person): bool { ... }

    /**
     * Determine if user can approve/reject (Admin only)
     */
    public function manageStatus(User $user, Person $person): bool
    {
        return $user->is_admin;
    }
}
