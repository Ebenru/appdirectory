<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Support\Facades\DB; // For raw queries
use Carbon\Carbon; // For timestamps

class AdminController extends Controller
{
    // --- Basic Authorization (Refine Later with Middleware/Roles) ---
    // You MUST implement proper authorization later. This is insecure.
    //private function checkAdmin() {
    //     if (!Auth::check()) { // || !Auth::user()->is_admin - check admin flag later
    //         abort(403, 'Unauthorized action.');
    //     }
    //}
    // --- End Basic Authorization ---

    /**
     * Display a list of pending submissions.
     */
    public function pendingList()
    {
        //$this->checkAdmin(); // Temporary check

        $pendingPeople = Person::where('status', 'pending')->with('submittedBy')->latest()->get();
        $pendingCompanies = Company::where('status', 'pending')->with('submittedBy')->latest()->get();

        return view('admin.pending', [
            'pendingPeople' => $pendingPeople,
            'pendingCompanies' => $pendingCompanies,
        ]);
    }

    /**
     * Approve a pending Person submission.
     */
    public function approvePerson(Person $person): RedirectResponse
    {
         //$this->checkAdmin();

        if ($person->status === 'pending') {
            $person->update([
                'status' => 'approved',
                'approved_by_id' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]);
            return redirect()->route('admin.pending')->with('success', $person->fullName . ' has been approved.');
        }
        return redirect()->route('admin.pending')->with('error', $person->fullName . ' could not be approved (invalid status).');
    }

    /**
     * Approve a pending Company submission.
     */
    public function approveCompany(Company $company): RedirectResponse
    {
        //$this->checkAdmin();

         if ($company->status === 'pending') {
            $company->update([
                'status' => 'approved',
                'approved_by_id' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]);
            return redirect()->route('admin.pending')->with('success', ($company->displayName ?? $company->legalName) . ' has been approved.');
        }
         return redirect()->route('admin.pending')->with('error', ($company->displayName ?? $company->legalName) . ' could not be approved (invalid status).');
    }

     /**
     * Reject a pending Person submission.
     */
    public function rejectPerson(Person $person): RedirectResponse
    {
         //$this->checkAdmin();

         if ($person->status === 'pending') {
            $person->update(['status' => 'rejected']); // Optionally clear approved_by/at
            return redirect()->route('admin.pending')->with('success', $person->fullName . ' has been rejected.');
        }
        return redirect()->route('admin.pending')->with('error', $person->fullName . ' could not be rejected (invalid status).');
    }

     /**
     * Reject a pending Company submission.
     */
    public function rejectCompany(Company $company): RedirectResponse
    {
        // $this->checkAdmin();

         if ($company->status === 'pending') {
            $company->update(['status' => 'rejected']);
            return redirect()->route('admin.pending')->with('success', ($company->displayName ?? $company->legalName) . ' has been rejected.');
        }
         return redirect()->route('admin.pending')->with('error', ($company->displayName ?? $company->legalName) . ' could not be rejected (invalid status).');
    }

    /**
     * Display the admin dashboard with KPIs.
     */
    public function dashboard()
    {
        // --- Core Content KPIs ---
        $pendingPeopleCount = Person::where('status', 'pending')->count();
        $pendingCompanyCount = Company::where('status', 'pending')->count();
        $approvedPeopleCount = Person::where('status', 'approved')->count();
        $approvedCompanyCount = Company::where('status', 'approved')->count();
        $totalSubmissionsLast30d = Person::where('created_at', '>=', now()->subDays(30))->count()
                                   + Company::where('created_at', '>=', now()->subDays(30))->count();

        // --- User Engagement KPIs ---
        $totalUsers = User::count();
        $newUsersLast30d = User::where('created_at', '>=', now()->subDays(30))->count();
        $totalLikes = Like::count();
        $totalComments = Comment::count(); // Includes replies for now

        // --- Recent Activity ---
        $recentPendingPeople = Person::where('status', 'pending')
                                    ->with('submittedBy')
                                    ->latest() // Order by created_at desc
                                    ->limit(5)
                                    ->get();

         $recentPendingCompanies = Company::where('status', 'pending')
                                    ->with('submittedBy')
                                    ->latest()
                                    ->limit(5)
                                    ->get();

         $recentUsers = User::where('is_admin', false) // Exclude admin
                            ->latest()
                            ->limit(5)
                            ->get();

        // --- Data for Charts (Example: Submissions per day for last week) ---
        $submissionsChartData = $this->getSubmissionChartData(7);


        // Pass data to the view
        return view('admin.dashboard', [
            'pendingPeopleCount' => $pendingPeopleCount,
            'pendingCompanyCount' => $pendingCompanyCount,
            'approvedPeopleCount' => $approvedPeopleCount,
            'approvedCompanyCount' => $approvedCompanyCount,
            'totalSubmissionsLast30d' => $totalSubmissionsLast30d,
            'totalUsers' => $totalUsers,
            'newUsersLast30d' => $newUsersLast30d,
            'totalLikes' => $totalLikes,
            'totalComments' => $totalComments,
            'recentPendingPeople' => $recentPendingPeople,
            'recentPendingCompanies' => $recentPendingCompanies,
            'recentUsers' => $recentUsers,
            'submissionsChartData' => $submissionsChartData,
        ]);
    }


    /**
     * Helper function to get submission data for a chart.
     * @param int $days Number of past days to fetch data for.
     * @return array
     */
    private function getSubmissionChartData(int $days = 7): array
    {
        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        // Get counts per day for People
        $peopleSubmissions = Person::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('count', 'date'); // Get as ['YYYY-MM-DD' => count]

         // Get counts per day for Companies
         $companySubmissions = Company::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('count', 'date');

        $labels = [];
        $peopleData = [];
        $companyData = [];
        $currentDate = $startDate->copy();

        // Generate labels and data for each day in the range
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $labels[] = $currentDate->format('M d'); // Format like 'Jan 01'
            $peopleData[] = $peopleSubmissions[$dateString] ?? 0;
            $companyData[] = $companySubmissions[$dateString] ?? 0;
            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'People Submissions',
                    'data' => $peopleData,
                    'borderColor' => 'hsl(var(--primary))', // Use theme color
                    'backgroundColor' => 'hsla(var(--primary), 0.2)', // Lighter version
                    'tension' => 0.1,
                    'fill' => true,
                ],
                [
                    'label' => 'Company Submissions',
                    'data' => $companyData,
                    'borderColor' => 'hsl(var(--secondary))',
                    'backgroundColor' => 'hsla(var(--secondary), 0.2)',
                    'tension' => 0.1,
                    'fill' => true,
                ],
            ]
        ];
    }
}