<?php

namespace App\Policies;

use App\Models\Company; // Change model
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    // viewAny - Allow anyone
    public function viewAny(?User $user): bool
    {
        return true;
    }

    // view - Allow if approved, or if admin/submitter
    public function view(?User $user, Company $company): bool
    {
        if ($company->status === 'approved') return true;
        return $user && ($user->is_admin || $user->id === $company->submitted_by_id);
    }

    // create - Allow any logged-in user
    public function create(User $user): bool
    {
        return true;
    }

    // update - Allow admin, or submitter if pending
    public function update(User $user, Company $company): bool
    {
        if ($user->is_admin) return true;
        return $user->id === $company->submitted_by_id && $company->status === 'pending';
    }

    // delete - Allow admin, or submitter if pending
    public function delete(User $user, Company $company): bool
    {
        if ($user->is_admin) return true;
        return $user->id === $company->submitted_by_id && $company->status === 'pending';
    }

    // manageStatus - Admin only
    public function manageStatus(User $user, Company $company): bool
    {
        return $user->is_admin;
    }
}
