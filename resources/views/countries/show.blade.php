{{-- resources/views/countries/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            {{-- Display Flag Icon if available --}}
            {{-- @if($country->flag_icon_url)
                <img src="{{ $country->flag_icon_url }}" class="h-8 w-auto" alt="{{ $country->name }} flag">
            @endif --}}
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Contributors from {{ $country->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Basic Country Info Card (Optional) --}}
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>{{ $country->name }} ({{ $country->iso_code }})</x-ui.card-title>
                    @if($country->region)
                    <x-ui.card-description>Region: {{ $country->region }}</x-ui.card-description>
                    @endif
                </x-ui.card-header>
                {{-- Add more country details here if needed --}}
            </x-ui.card>


            {{-- Related People Section --}}
            @if($country->people->isNotEmpty())
            <section>
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Featured People from {{ $country->name }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($country->people as $person)
                    {{-- Reuse the card structure from people.index, maybe create a partial/component --}}
                    <x-ui.card class="overflow-hidden transition-all hover:shadow-lg">
                        <a href="{{ route('people.show', $person) }}" class="block">
                            <img src="{{ $person->display_photo_url }}" alt="{{ $person->fullName }}" class="w-full h-48 object-cover">
                            <x-ui.card-header>
                                <x-ui.card-title class="text-lg">{{ $person->fullName }}</x-ui.card-title>
                                @if($person->title)
                                <x-ui.card-description>{{ $person->title }}</x-ui.card-description>
                                @endif
                            </x-ui.card-header>
                            <x-ui.card-content class="min-h-[60px]"> {{-- Set min-height --}}
                                <p class="text-sm text-muted-foreground line-clamp-2"> {{-- Limit lines --}}
                                    {{ $person->description }}
                                </p>
                            </x-ui.card-content>
                            {{-- Optional footer with category/like --}}
                            <x-ui.card-footer class="text-xs text-muted-foreground pt-4 border-t dark:border-gray-700 flex justify-between items-center">
                                <span>Cat: {{ $person->category_display_name }}</span>
                                {{-- @include('partials._like_button', ['likeable' => $person, 'type' => 'person']) --}}
                            </x-ui.card-footer>
                        </a>
                    </x-ui.card>
                    @endforeach
                </div>
                {{-- Link to full people list filtered by country? --}}
                {{-- @if($country->people()->where('status','approved')->count() > 12)
                        <div class="mt-4 text-center">
                            <a href="{{ route('people.index', ['country' => $country->slug]) }}" class="text-sm text-primary hover:underline">View all people from {{ $country->name }} →</a>
        </div>
        @endif --}}
        </section>
        @endif


        {{-- Related Companies Section --}}
        @if($country->companies->isNotEmpty())
        <section>
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Featured Organizations from {{ $country->name }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($country->companies as $company)
                {{-- Reuse the card structure from companies.index --}}
                <x-ui.card class="overflow-hidden transition-all hover:shadow-lg">
                    <a href="{{ route('companies.show', $company) }}" class="block">
                        <div class="w-full h-48 bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-4">
                            <img src="{{ $company->display_logo_url }}" alt="{{ $company->displayName ?? $company->legalName }} Logo" class="max-w-full max-h-full object-contain">
                        </div>
                        <x-ui.card-header>
                            <x-ui.card-title class="text-lg">{{ $company->displayName ?? $company->legalName }}</x-ui.card-title>
                            @if($company->displayName && $company->displayName != $company->legalName)
                            <x-ui.card-description>Legal: {{ $company->legalName }}</x-ui.card-description>
                            @endif
                        </x-ui.card-header>
                        <x-ui.card-content class="min-h-[60px]">
                            <p class="text-sm text-muted-foreground line-clamp-2">
                                {{ $company->description }}
                            </p>
                        </x-ui.card-content>
                        <x-ui.card-footer class="text-xs text-muted-foreground pt-4 border-t dark:border-gray-700 flex justify-between items-center">
                            <span>Cat: {{ $company->category_display_name }}</span>
                            {{-- @include('partials._like_button', ['likeable' => $company, 'type' => 'company']) --}}
                        </x-ui.card-footer>
                    </a>
                </x-ui.card>
                @endforeach
            </div>
            {{-- Link to full companies list filtered by country? --}}
            {{-- @if($country->companies()->where('status','approved')->count() > 12) ... @endif --}}
        </section>
        @endif

        {{-- TODO: Add sections for related Groups, Organizations, Events when implemented --}}


        {{-- Add Comments/Likes specifically for the Country page? (Optional) --}}
        {{-- If Countries themselves can be liked/commented on: --}}
        {{-- @include('partials._like_button', ['likeable' => $country, 'type' => 'country']) --}}
        {{-- @include('partials._comment_section', ['commentable' => $country, 'type' => 'country']) --}}

    </div>
    </div>
</x-app-layout>{{-- resources/views/countries/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            {{-- Display Flag Icon if available --}}
            {{-- @if($country->flag_icon_url)
                <img src="{{ $country->flag_icon_url }}" class="h-8 w-auto" alt="{{ $country->name }} flag">
            @endif --}}
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Contributors from {{ $country->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Basic Country Info Card (Optional) --}}
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>{{ $country->name }} ({{ $country->iso_code }})</x-ui.card-title>
                    @if($country->region)
                    <x-ui.card-description>Region: {{ $country->region }}</x-ui.card-description>
                    @endif
                </x-ui.card-header>
                {{-- Add more country details here if needed --}}
            </x-ui.card>


            {{-- Related People Section --}}
            @if($country->people->isNotEmpty())
            <section>
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Featured People from {{ $country->name }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($country->people as $person)
                    {{-- Reuse the card structure from people.index, maybe create a partial/component --}}
                    <x-ui.card class="overflow-hidden transition-all hover:shadow-lg">
                        <a href="{{ route('people.show', $person) }}" class="block">
                            <img src="{{ $person->display_photo_url }}" alt="{{ $person->fullName }}" class="w-full h-48 object-cover">
                            <x-ui.card-header>
                                <x-ui.card-title class="text-lg">{{ $person->fullName }}</x-ui.card-title>
                                @if($person->title)
                                <x-ui.card-description>{{ $person->title }}</x-ui.card-description>
                                @endif
                            </x-ui.card-header>
                            <x-ui.card-content class="min-h-[60px]"> {{-- Set min-height --}}
                                <p class="text-sm text-muted-foreground line-clamp-2"> {{-- Limit lines --}}
                                    {{ $person->description }}
                                </p>
                            </x-ui.card-content>
                            {{-- Optional footer with category/like --}}
                            <x-ui.card-footer class="text-xs text-muted-foreground pt-4 border-t dark:border-gray-700 flex justify-between items-center">
                                <span>Cat: {{ $person->category_display_name }}</span>
                                {{-- @include('partials._like_button', ['likeable' => $person, 'type' => 'person']) --}}
                            </x-ui.card-footer>
                        </a>
                    </x-ui.card>
                    @endforeach
                </div>
                {{-- Link to full people list filtered by country? --}}
                {{-- @if($country->people()->where('status','approved')->count() > 12)
                        <div class="mt-4 text-center">
                            <a href="{{ route('people.index', ['country' => $country->slug]) }}" class="text-sm text-primary hover:underline">View all people from {{ $country->name }} →</a>
        </div>
        @endif --}}
        </section>
        @endif


        {{-- Related Companies Section --}}
        @if($country->companies->isNotEmpty())
        <section>
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Featured Organizations from {{ $country->name }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($country->companies as $company)
                {{-- Reuse the card structure from companies.index --}}
                <x-ui.card class="overflow-hidden transition-all hover:shadow-lg">
                    <a href="{{ route('companies.show', $company) }}" class="block">
                        <div class="w-full h-48 bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-4">
                            <img src="{{ $company->display_logo_url }}" alt="{{ $company->displayName ?? $company->legalName }} Logo" class="max-w-full max-h-full object-contain">
                        </div>
                        <x-ui.card-header>
                            <x-ui.card-title class="text-lg">{{ $company->displayName ?? $company->legalName }}</x-ui.card-title>
                            @if($company->displayName && $company->displayName != $company->legalName)
                            <x-ui.card-description>Legal: {{ $company->legalName }}</x-ui.card-description>
                            @endif
                        </x-ui.card-header>
                        <x-ui.card-content class="min-h-[60px]">
                            <p class="text-sm text-muted-foreground line-clamp-2">
                                {{ $company->description }}
                            </p>
                        </x-ui.card-content>
                        <x-ui.card-footer class="text-xs text-muted-foreground pt-4 border-t dark:border-gray-700 flex justify-between items-center">
                            <span>Cat: {{ $company->category_display_name }}</span>
                            {{-- @include('partials._like_button', ['likeable' => $company, 'type' => 'company']) --}}
                        </x-ui.card-footer>
                    </a>
                </x-ui.card>
                @endforeach
            </div>
            {{-- Link to full companies list filtered by country? --}}
            {{-- @if($country->companies()->where('status','approved')->count() > 12) ... @endif --}}
        </section>
        @endif

        {{-- TODO: Add sections for related Groups, Organizations, Events when implemented --}}


        {{-- Add Comments/Likes specifically for the Country page? (Optional) --}}
        {{-- If Countries themselves can be liked/commented on: --}}
        {{-- @include('partials._like_button', ['likeable' => $country, 'type' => 'country']) --}}
        {{-- @include('partials._comment_section', ['commentable' => $country, 'type' => 'country']) --}}

    </div>
    </div>
</x-app-layout>{{-- resources/views/countries/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            {{-- Display Flag Icon if available --}}
            {{-- @if($country->flag_icon_url)
                <img src="{{ $country->flag_icon_url }}" class="h-8 w-auto" alt="{{ $country->name }} flag">
            @endif --}}
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Contributors from {{ $country->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Basic Country Info Card (Optional) --}}
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>{{ $country->name }} ({{ $country->iso_code }})</x-ui.card-title>
                    @if($country->region)
                    <x-ui.card-description>Region: {{ $country->region }}</x-ui.card-description>
                    @endif
                </x-ui.card-header>
                {{-- Add more country details here if needed --}}
            </x-ui.card>


            {{-- Related People Section --}}
            @if($country->people->isNotEmpty())
            <section>
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Featured People from {{ $country->name }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($country->people as $person)
                    {{-- Reuse the card structure from people.index, maybe create a partial/component --}}
                    <x-ui.card class="overflow-hidden transition-all hover:shadow-lg">
                        <a href="{{ route('people.show', $person) }}" class="block">
                            <img src="{{ $person->display_photo_url }}" alt="{{ $person->fullName }}" class="w-full h-48 object-cover">
                            <x-ui.card-header>
                                <x-ui.card-title class="text-lg">{{ $person->fullName }}</x-ui.card-title>
                                @if($person->title)
                                <x-ui.card-description>{{ $person->title }}</x-ui.card-description>
                                @endif
                            </x-ui.card-header>
                            <x-ui.card-content class="min-h-[60px]"> {{-- Set min-height --}}
                                <p class="text-sm text-muted-foreground line-clamp-2"> {{-- Limit lines --}}
                                    {{ $person->description }}
                                </p>
                            </x-ui.card-content>
                            {{-- Optional footer with category/like --}}
                            <x-ui.card-footer class="text-xs text-muted-foreground pt-4 border-t dark:border-gray-700 flex justify-between items-center">
                                <span>Cat: {{ $person->category_display_name }}</span>
                                {{-- @include('partials._like_button', ['likeable' => $person, 'type' => 'person']) --}}
                            </x-ui.card-footer>
                        </a>
                    </x-ui.card>
                    @endforeach
                </div>
                {{-- Link to full people list filtered by country? --}}
                {{-- @if($country->people()->where('status','approved')->count() > 12)
                        <div class="mt-4 text-center">
                            <a href="{{ route('people.index', ['country' => $country->slug]) }}" class="text-sm text-primary hover:underline">View all people from {{ $country->name }} →</a>
        </div>
        @endif --}}
        </section>
        @endif


        {{-- Related Companies Section --}}
        @if($country->companies->isNotEmpty())
        <section>
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Featured Organizations from {{ $country->name }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($country->companies as $company)
                {{-- Reuse the card structure from companies.index --}}
                <x-ui.card class="overflow-hidden transition-all hover:shadow-lg">
                    <a href="{{ route('companies.show', $company) }}" class="block">
                        <div class="w-full h-48 bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-4">
                            <img src="{{ $company->display_logo_url }}" alt="{{ $company->displayName ?? $company->legalName }} Logo" class="max-w-full max-h-full object-contain">
                        </div>
                        <x-ui.card-header>
                            <x-ui.card-title class="text-lg">{{ $company->displayName ?? $company->legalName }}</x-ui.card-title>
                            @if($company->displayName && $company->displayName != $company->legalName)
                            <x-ui.card-description>Legal: {{ $company->legalName }}</x-ui.card-description>
                            @endif
                        </x-ui.card-header>
                        <x-ui.card-content class="min-h-[60px]">
                            <p class="text-sm text-muted-foreground line-clamp-2">
                                {{ $company->description }}
                            </p>
                        </x-ui.card-content>
                        <x-ui.card-footer class="text-xs text-muted-foreground pt-4 border-t dark:border-gray-700 flex justify-between items-center">
                            <span>Cat: {{ $company->category_display_name }}</span>
                            {{-- @include('partials._like_button', ['likeable' => $company, 'type' => 'company']) --}}
                        </x-ui.card-footer>
                    </a>
                </x-ui.card>
                @endforeach
            </div>
            {{-- Link to full companies list filtered by country? --}}
            {{-- @if($country->companies()->where('status','approved')->count() > 12) ... @endif --}}
        </section>
        @endif

        {{-- TODO: Add sections for related Groups, Organizations, Events when implemented --}}


        {{-- Add Comments/Likes specifically for the Country page? (Optional) --}}
        {{-- If Countries themselves can be liked/commented on: --}}
        {{-- @include('partials._like_button', ['likeable' => $country, 'type' => 'country']) --}}
        {{-- @include('partials._comment_section', ['commentable' => $country, 'type' => 'country']) --}}

    </div>
    </div>
</x-app-layout>