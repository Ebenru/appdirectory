<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Country;
use App\Models\Event;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\Request;

// app/Http/Controllers/SearchController.php
// ... (imports) ...

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');
        $selectedTypes = $request->input('types', []); // Expect 'types' array now

        $peopleResults = collect(); // Initialize empty collections
        $companyResults = collect();
        $groupResults = collect();
        $organizationResults = collect();
        $eventResults = collect();
        // Add others as needed

        $includePeople = empty($selectedTypes) || in_array('person', $selectedTypes);
        $includeCompany = empty($selectedTypes) || in_array('company', $selectedTypes);
        $includeGroup = empty($selectedTypes) || in_array('group', $selectedTypes);
        $includeOrganization = empty($selectedTypes) || in_array('organization', $selectedTypes);
        $includeEvent = empty($selectedTypes) || in_array('event', $selectedTypes);
        // Add others...

        // --- Build Person Query (Conditional) ---
        if ($includePeople) {
            $peopleQuery = Person::query()->where('status', 'approved');
            if ($searchQuery) {
                $peopleQuery->where(function ($q) use ($searchQuery) {
                    $q->where('fullName', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('title', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('description', 'LIKE', "%{$searchQuery}%");
                });
            }
            $peopleResults = $peopleQuery->orderBy('fullName')->limit(20)->get();
        }

        // --- Build Company Query (Conditional) ---
        if ($includeCompany) {
            $companyQuery = Company::query()->where('status', 'approved');
            if ($searchQuery) {
                $companyQuery->where(function ($q) use ($searchQuery) {
                    $q->where('legalName', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('displayName', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('description', 'LIKE', "%{$searchQuery}%");
                });
            }
            $companyResults = $companyQuery->orderBy('legalName')->limit(20)->get();
        }

        // --- Build Group Query (Conditional) ---
        if ($includeGroup) {
            $groupQuery = Group::query()->where('status', 'approved');
            if ($searchQuery) {
                $groupQuery->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('industry', 'LIKE', "%{$searchQuery}%");
                });
            }
            $groupResults = $groupQuery->orderBy('name')->limit(20)->get();
        }

        // --- Build Organization Query (Conditional) ---
        if ($includeOrganization) {
            $organizationQuery = Organization::query()->where('status', 'approved');
            if ($searchQuery) {
                $organizationQuery->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('type', 'LIKE', "%{$searchQuery}%");
                });
            }
            $organizationResults = $organizationQuery->orderBy('name')->limit(20)->get();
        }

        // --- Build Event Query (Conditional) ---
        if ($includeEvent) {
            $eventQuery = Event::query()->where('status', 'approved');
            if ($searchQuery) {
                $eventQuery->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('location', 'LIKE', "%{$searchQuery}%")
                        ->orWhere('eventType', 'LIKE', "%{$searchQuery}%");
                });
            }
            $eventResults = $eventQuery->orderBy('startDate', 'desc')->limit(20)->get();
        }


        // Available entities for the filter bar on the results page
        $availableEntities = [
            'person' => 'People',
            'company' => 'Companies',
            'group' => 'Groups',
            'organization' => 'Organizations',
            'event' => 'Events',
            'country' => 'Countries',
        ];


        return view('search.index', [
            'searchQuery' => $searchQuery,
            'selectedTypes' => $selectedTypes, // Pass selected types back
            'availableEntities' => $availableEntities, // Pass available types for filter display
            'peopleResults' => $peopleResults,
            'companyResults' => $companyResults,
            'groupResults' => $groupResults,
            'organizationResults' => $organizationResults,
            'eventResults' => $eventResults,
            // Pass other results...
        ]);
    }
}
