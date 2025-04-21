{{-- resources/views/search/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Search Results {{ $searchQuery ? 'for "' . $searchQuery . '"' : '' }}
        </h2>
    </x-slot>

    {{-- Include the *Livewire* search component here too for consistency --}}
    {{-- It will automatically pick up query params from the URL on load --}}
    <section class="w-full py-8 md:py-10 bg-gray-50 dark:bg-gray-800 border-b dark:border-gray-700">
        <livewire:landing-search-filter
            {{-- Pass available entities if needed by Livewire component (optional here) --}} />
    </section>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Determine active tab (e.g., first one with results) --}}
            @php
            $activeTab = 'people'; // Default
            if ($peopleResults->isEmpty()) $activeTab = 'companies';
            if ($peopleResults->isEmpty() && $companyResults->isEmpty()) $activeTab = 'groups';
            if ($peopleResults->isEmpty() && $companyResults->isEmpty() && $groupResults->isEmpty()) $activeTab = 'organizations';
            if ($peopleResults->isEmpty() && $companyResults->isEmpty() && $groupResults->isEmpty() && $organizationResults->isEmpty()) $activeTab = 'events';
            // Add more fallbacks...
            @endphp

            {{-- Results Tabs --}}
            <x-ui.tabs :defaultValue="$activeTab" class="w-full">
                {{-- Tab Triggers - Only show tabs if results exist for them OR no specific types were selected --}}
                <x-ui.tabs-list class="mb-8 flex flex-wrap justify-start">
                    @if($peopleResults->isNotEmpty() || empty($selectedTypes))
                    <x-ui.tabs-trigger value="people">
                        <x-lucide-user class="w-4 h-4 mr-2" /> People ({{ $peopleResults->count() }})
                    </x-ui.tabs-trigger>
                    @endif
                    @if($companyResults->isNotEmpty() || empty($selectedTypes))
                    <x-ui.tabs-trigger value="companies">
                        <x-lucide-building-2 class="w-4 h-4 mr-2" /> Orgs ({{ $companyResults->count() }})
                    </x-ui.tabs-trigger>
                    @endif
                    @if($groupResults->isNotEmpty() || empty($selectedTypes))
                    <x-ui.tabs-trigger value="groups">
                        <x-lucide-network class="w-4 h-4 mr-2" /> Groups ({{ $groupResults->count() }})
                    </x-ui.tabs-trigger>
                    @endif
                    @if($organizationResults->isNotEmpty() || empty($selectedTypes))
                    <x-ui.tabs-trigger value="organizations">
                        <x-lucide-library class="w-4 h-4 mr-2" /> Organizations ({{ $organizationResults->count() }})
                    </x-ui.tabs-trigger>
                    @endif
                    @if($eventResults->isNotEmpty() || empty($selectedTypes))
                    <x-ui.tabs-trigger value="events">
                        <x-lucide-calendar class="w-4 h-4 mr-2" /> Events ({{ $eventResults->count() }})
                    </x-ui.tabs-trigger>
                    @endif
                    {{-- Add triggers for other types --}}
                </x-ui.tabs-list>

                {{-- People Results Tab Content --}}
                @if($peopleResults->isNotEmpty() || empty($selectedTypes))
                <x-ui.tabs-content value="people">
                    @if($peopleResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($peopleResults as $person)
                        {{-- Include Person Card Partial/Component --}}
                        @include('partials._person_card', ['person' => $person])
                        @endforeach
                    </div>
                    @elseif(!empty($selectedTypes))
                    <p class="text-center text-muted-foreground py-10">No people found matching your specific criteria.</p>
                    @endif
                </x-ui.tabs-content>
                @endif

                {{-- Company Results Tab Content --}}
                @if($companyResults->isNotEmpty() || empty($selectedTypes))
                <x-ui.tabs-content value="companies">
                    @if($companyResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($companyResults as $company)
                        {{-- Include Company Card Partial/Component --}}
                        @include('partials._company_card', ['company' => $company])
                        @endforeach
                    </div>
                    @elseif(!empty($selectedTypes))
                    <p class="text-center text-muted-foreground py-10">No companies found matching your specific criteria.</p>
                    @endif
                </x-ui.tabs-content>
                @endif

                {{-- Group Results Tab Content --}}
                @if($groupResults->isNotEmpty() || empty($selectedTypes))
                <x-ui.tabs-content value="groups">
                    @if($groupResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($groupResults as $group)
                        @include('partials._group_card', ['group' => $group]) {{-- Create this partial --}}
                        @endforeach
                    </div>
                    @elseif(!empty($selectedTypes))
                    <p class="text-center text-muted-foreground py-10">No groups found matching your specific criteria.</p>
                    @endif
                </x-ui.tabs-content>
                @endif

                {{-- Organization Results Tab Content --}}
                @if($organizationResults->isNotEmpty() || empty($selectedTypes))
                <x-ui.tabs-content value="organizations">
                    @if($organizationResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($organizationResults as $organization)
                        @include('partials._organization_card', ['organization' => $organization]) {{-- Create this partial --}}
                        @endforeach
                    </div>
                    @elseif(!empty($selectedTypes))
                    <p class="text-center text-muted-foreground py-10">No organizations found matching your specific criteria.</p>
                    @endif
                </x-ui.tabs-content>
                @endif

                {{-- Event Results Tab Content --}}
                @if($eventResults->isNotEmpty() || empty($selectedTypes))
                <x-ui.tabs-content value="events">
                    @if($eventResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($eventResults as $event)
                        @include('partials._event_card', ['event' => $event]) {{-- Create this partial --}}
                        @endforeach
                    </div>
                    @elseif(!empty($selectedTypes))
                    <p class="text-center text-muted-foreground py-10">No events found matching your specific criteria.</p>
                    @endif
                </x-ui.tabs-content>
                @endif

                {{-- Add Content blocks for other types --}}

                {{-- Message if NO results found at all --}}
                @if($peopleResults->isEmpty() && $companyResults->isEmpty() && $groupResults->isEmpty() && $organizationResults->isEmpty() && $eventResults->isEmpty() && !empty($selectedTypes))
                <p class="text-center text-muted-foreground py-10 text-lg">
                    No results found matching your selected types and search query.
                </p>
                @elseif(
                $peopleResults->isEmpty()
                && $companyResults->isEmpty()
                && $groupResults->isEmpty()
                && $organizationResults->isEmpty()
                && $eventResults->isEmpty()
                && empty($selectedTypes)
                && $searchQuery)
                <p class="text-center text-muted-foreground py-10 text-lg">
                    No results found for "{{ $searchQuery }}". Try refining your search.
                    <button wire:click="clearAllFilters" class="text-sm text-primary hover:underline ml-2">Clear Search & Filters</button> {{-- Add clear button here too --}}
                </p>
                @endif


            </x-ui.tabs>

        </div>
    </div>
</x-app-layout>