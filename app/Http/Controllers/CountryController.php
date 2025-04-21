<?php

namespace App\Http\Controllers;

use App\Models\Country; // Import Country
use Illuminate\Http\Request;
use Illuminate\View\View;

class CountryController extends Controller
{
    /**
     * Display a listing of all countries.
     */
    public function index(): View
    {
        $countries = Country::orderBy('name')
            // Optionally count related items if needed for index page
            // ->withCount(['people' => fn($q) => $q->where('status', 'approved')])
            // ->withCount(['companies' => fn($q) => $q->where('status', 'approved')])
            ->paginate(12); // Paginate if the list might grow large

        return view('countries.index', ['countries' => $countries]);
    }

    /**
     * Display the specified country and its related approved entities.
     * Route model binding automatically finds the Country by its slug.
     */
    public function show(Country $country): View // Use route model binding
    {
        // Eager load relationships with approved status filter
        $country->load([
            'people' => function ($query) {
                $query->where('status', 'approved')->orderBy('fullName')->limit(12); // Load approved people
            },
            'companies' => function ($query) {
                $query->where('status', 'approved')->orderBy('legalName')->limit(12); // Load approved companies
            },
            // Add 'groups', 'organizations', 'events' later when implemented
            // 'events' => fn($q) => $q->where('status', 'approved')->orderBy('startDate'),
        ]);

        // You could fetch counts separately if needed without loading all models
        // $approvedPeopleCount = $country->people()->where('status', 'approved')->count();

        return view('countries.show', ['country' => $country]);
    }
}
