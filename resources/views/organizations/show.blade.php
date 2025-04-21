{{-- resources/views/organizations/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Organization: {{ $organization->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Main Details Card --}}
            <x-ui.card>
                <div class="p-4 sm:p-6 flex flex-col md:flex-row gap-6">
                    {{-- Logo Column --}}
                    <div class="flex-shrink-0 md:w-1/4 flex items-center justify-center">
                        <div class="h-40 w-full p-1 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md">
                            <img class="max-h-full max-w-full object-contain" src="{{ $organization->display_logo_url }}" alt="{{ $organization->name }}">
                        </div>
                    </div>

                    {{-- Details Column --}}
                    <div class="flex-grow">
                        {{-- Header Row with Like Button --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-2xl font-semibold leading-7 text-gray-900 dark:text-white">{{ $organization->name }}</h3>
                                @if($organization->type)
                                <p class="mt-1 max-w-2xl text-md leading-6 text-gray-500 dark:text-gray-400">{{ $organization->type }}</p>
                                @endif
                                @if($organization->website_url)
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-primary hover:underline">
                                    <a href="{{ $organization->website_url }}" target="_blank" rel="noopener noreferrer">
                                        Visit Website <x-lucide-external-link class="inline-block h-3 w-3 ml-1" />
                                    </a>
                                </p>
                                @endif
                            </div>
                            {{-- Include Like Button partial (assuming Organizations are likeable) --}}
                            @include('partials._like_button', ['likeable' => $organization, 'type' => 'organization'])
                        </div>

                        {{-- Details List (dl) --}}
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Type --}}
                                @if($organization->type)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">{{ $organization->type }}</dd>
                                </div>
                                @endif
                                {{-- Scope --}}
                                @if($organization->scope)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Scope</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">{{ $organization->scope }}</dd>
                                </div>
                                @endif
                                {{-- Founding Year --}}
                                @if($organization->foundingYear)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Founded</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">{{ $organization->foundingYear }}</dd>
                                </div>
                                @endif
                                {{-- Country --}}
                                @if($organization->country)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        <a href="{{ route('countries.show', $organization->country) }}" class="text-primary hover:underline">
                                            {{ $organization->country->name }}
                                        </a>
                                    </dd>
                                </div>
                                @endif
                                {{-- Description --}}
                                @if($organization->description)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">About</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $organization->description }}</dd>
                                </div>
                                @endif
                                {{-- Key Achievements --}}
                                @if($organization->key_achievements)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Key Info/Achievements</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $organization->key_achievements }}</dd>
                                </div>
                                @endif
                                {{-- Tags, Social, Sources, Featured Article... (Copy sections from other show views) --}}
                                @include('partials._show_common_attributes', ['entity' => $organization])

                                {{-- TODO: Add Admin Edit Button conditionally --}}

                            </dl>
                        </div>
                    </div> {{-- End Details Column --}}
                </div>
            </x-ui.card>

            {{-- TODO: Add Sections for Related People, Companies, Events --}}

            {{-- Comments Section (assuming Organizations are commentable) --}}
            <div class="mt-12 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    @include('partials._comment_section', [
                    'commentable' => $organization,
                    'type' => 'organization',
                    'comments' => $comments, /* Pass comments from controller */
                    'currentSort' => $currentSort
                    ])
                </div>
            </div>

        </div>
    </div>
    @push('scripts') @endpush {{-- For comment reply JS --}}
</x-app-layout>