<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Person; // Import needed models
use App\Models\Company;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard with stats.
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // --- Calculate Stats ---

        // Total Submissions by User
        $peopleSubmittedCount = Person::where('submitted_by_id', $userId)->count();
        $companiesSubmittedCount = Company::where('submitted_by_id', $userId)->count();
        $totalSubmissions = $peopleSubmittedCount + $companiesSubmittedCount;

        // Submission Status Counts
        $pendingSubmissions = Person::where('submitted_by_id', $userId)->where('status', 'pending')->count()
            + Company::where('submitted_by_id', $userId)->where('status', 'pending')->count();
        $approvedSubmissions = Person::where('submitted_by_id', $userId)->where('status', 'approved')->count()
            + Company::where('submitted_by_id', $userId)->where('status', 'approved')->count();
        $rejectedSubmissions = Person::where('submitted_by_id', $userId)->where('status', 'rejected')->count()
            + Company::where('submitted_by_id', $userId)->where('status', 'rejected')->count();

        // Likes received on user's *approved* submissions (More complex query)
        // Get IDs of user's approved people/companies
        $approvedPersonIds = Person::where('submitted_by_id', $userId)->where('status', 'approved')->pluck('id');
        $approvedCompanyIds = Company::where('submitted_by_id', $userId)->where('status', 'approved')->pluck('id');

        $likesOnPeople = Like::where('likeable_type', Person::class) // Use Model class directly
            ->whereIn('likeable_id', $approvedPersonIds)
            ->count();
        $likesOnCompanies = Like::where('likeable_type', Company::class)
            ->whereIn('likeable_id', $approvedCompanyIds)
            ->count();
        $totalLikesReceived = $likesOnPeople + $likesOnCompanies;

        // TODO: Notifications (Requires a notification system implementation first)
        $notifications = []; // Placeholder

        return view('dashboard', [
            'user' => $user,
            'totalSubmissions' => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'approvedSubmissions' => $approvedSubmissions,
            'rejectedSubmissions' => $rejectedSubmissions,
            'totalLikesReceived' => $totalLikesReceived,
            'notifications' => $notifications, // Pass placeholder
        ]);
    }

    /**
     * Display the user's own submissions.
     */
    public function submissions(): View
    {
        $user = Auth::user();
        $statusOrder = "CASE status
                            WHEN 'pending' THEN 1
                            WHEN 'approved' THEN 2
                            WHEN 'rejected' THEN 3
                            ELSE 4
                        END";

        // --- Build the People Query ---
        $peopleQuery = Person::where('submitted_by_id', $user->id) // Start query
            ->orderByRaw($statusOrder . ' ASC')       // Apply sorting to builder
            ->orderByDesc('created_at');              // Apply sorting to builder

        // --- Execute the People Query ---
        $people = $peopleQuery->paginate(12, ['*'], 'people_page'); // Execute with paginate


        // --- Build the Companies Query ---
        $companiesQuery = Company::where('submitted_by_id', $user->id) // Start query
            ->orderByRaw($statusOrder . ' ASC')         // Apply sorting to builder
            ->orderByDesc('created_at');                // Apply sorting to builder

        // --- Execute the Companies Query ---
        $companies = $companiesQuery->paginate(12, ['*'], 'companies_page'); // Execute with paginate


        // --- Pass results to the view ---
        return view('my-submissions', compact('people', 'companies')); // Adjust view name
    }
}
