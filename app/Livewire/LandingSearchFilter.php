<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class LandingSearchFilter extends Component
{
    // Define available entity types for filtering
    public $availableEntities = [
        'person' => 'People',
        'company' => 'Companies',
        'group' => 'Groups',
        'organization' => 'Organizations',
        'event' => 'Events',
        'country' => 'Countries',
    ];

    // State managed by Livewire
    public $searchQuery = '';
    public $selectedEntities = []; // Array of selected entity type slugs
    // REMOVED: public $showFilters = false;
    public $currentRouteName;
    public function mount()
    {
        $this->searchQuery = request()->query('search', '');
        $this->selectedEntities = request()->query('types', []); // Pre-fill from URL
        $this->currentRouteName = Route::currentRouteName();
    }

    // REMOVED: public function toggleFilters() { ... }

    // --- NEW: Method to toggle an entity type ---
    public function toggleEntityType(string $type)
    {
        if (in_array($type, $this->selectedEntities)) {
            // Remove if already selected
            $this->selectedEntities = array_diff($this->selectedEntities, [$type]);
        } else {
            // Add if not selected
            $this->selectedEntities[] = $type;
        }
        // Ensure array keys are reset if needed, though usually not necessary here
        $this->selectedEntities = array_values($this->selectedEntities);
    }
    // --- END NEW METHOD ---


    public function clearAllFilters() // Renamed method
    {
        $this->selectedEntities = [];
        $this->searchQuery = ''; // Also clear search query on full clear

        // Only perform search if current route is NOT homepage
        if ($this->currentRouteName !== 'landing') {
            $this->performSearch();
        }
    }


    public function performSearch()
    {
        $params = [];

        if (trim($this->searchQuery)) {
            $params['search'] = trim($this->searchQuery);
        }

        $redirectUrl = route('search.index'); // Default: unified search page

        // --- UPDATED REDIRECT LOGIC ---
        if (count($this->selectedEntities) === 1) {
            $entityType = $this->selectedEntities[0];
            $routeName = match ($entityType) {
                'person' => 'people.index',
                'company' => 'companies.index',
                'group' => 'groups.index',
                'organization' => 'organizations.index',
                'event' => 'events.index',
                'country' => 'countries.index',
                default => null,
            };
            // Only redirect to specific index if *only* that type is selected
            // AND there is no search query. Otherwise, use the unified search page.
            if ($routeName && !trim($this->searchQuery)) {
                $redirectUrl = route($routeName);
                // Pass search query even if redirecting to index? Optional.
                // if (isset($params['search'])) {
                //     $redirectUrl .= '?search=' . urlencode($params['search']);
                // }
            } else {
                // Send to unified search page if search term exists OR multiple types selected
                $params['types'] = $this->selectedEntities;
                $redirectUrl = route('search.index');
            }
        } else if (count($this->selectedEntities) > 1) {
            $params['types'] = $this->selectedEntities;
            $redirectUrl = route('search.index');
        }
        // If no types selected & no search, could redirect to landing or people index
        // Currently defaults to search index if search term present, otherwise stays on landing


        $queryString = http_build_query(array_filter($params));

        // Perform standard redirect
        return redirect()->to($redirectUrl . ($queryString ? '?' . $queryString : ''));
    }

    public function render()
    {
        return view('livewire.landing-search-filter');
    }
}
