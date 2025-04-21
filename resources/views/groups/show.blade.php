{{-- resources/views/groups/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Group: {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Main Details Card using Components --}}
            <x-ui.card>
                <div class="p-4 sm:p-6 flex flex-col md:flex-row gap-6">
                    {{-- Logo Column - Reuse DisplayImage component? Or simple img tag --}}
                    <div class="flex-shrink-0 md:w-1/4 flex items-center justify-center">
                        <div class="h-40 w-full p-1 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md">
                            <img class="max-h-full max-w-full object-contain" src="{{ $group->display_logo_url }}" alt="{{ $group->name }}">
                        </div>
                    </div>

                    {{-- Details Column --}}
                    <div class="flex-grow">
                        {{-- Header Row with Like Button --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                {{-- Use EntityHeader component? --}}
                                <h3 class="text-2xl font-semibold leading-7 text-gray-900 dark:text-white">{{ $group->name }}</h3>
                                @if($group->industry)
                                <p class="mt-1 max-w-2xl text-md leading-6 text-gray-500 dark:text-gray-400">Industry: {{ $group->industry }}</p>
                                @endif
                                @if($group->website_url)
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-primary hover:underline">
                                    <a href="{{ $group->website_url }}" target="_blank" rel="noopener noreferrer">
                                        Visit Website <x-lucide-external-link class="inline-block h-3 w-3 ml-1" />
                                    </a>
                                </p>
                                @endif
                            </div>
                            {{-- Include Like Button partial (assuming Groups are likeable) --}}
                            {{-- Create Like model/migration entry if Groups weren't included before --}}
                            @include('partials._like_button', ['likeable' => $group, 'type' => 'group'])
                        </div>

                        {{-- Details List (dl) - Use AttributeList component? --}}
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Description --}}
                                @if($group->description)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">About</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $group->description }}</dd>
                                </div>
                                @endif

                                {{-- Country --}}
                                @if($group->country)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <a href="{{ route('countries.show', $group->country) }}" class="text-primary hover:underline">
                                            {{ $group->country->name }}
                                        </a>
                                    </dd>
                                </div>
                                @endif

                                {{-- Tags --}}
                                @if($group->tags->isNotEmpty())
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($group->tags as $tag) <x-ui.badge variant="secondary">{{ $tag->name }}</x-ui.badge> @endforeach
                                        </div>
                                    </dd>
                                </div>
                                @endif

                                {{-- Social Media --}}
                                @if(!empty($group->social_media))
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    {{-- ... dt/dd for social links ... --}}
                                </div>
                                @endif

                                {{-- Sources --}}
                                @if(!empty($group->sources))
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    {{-- ... dt/dd for sources ... --}}
                                </div>
                                @endif

                                {{-- Key Achievements --}}
                                @if($group->key_achievements)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Key Info</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $group->key_achievements }}</dd>
                                </div>
                                @endif

                                {{-- Featured Article --}}
                                @if($group->featured_article_url)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Featured Link</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        <a href="{{ $group->featured_article_url }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline break-all">...</a>
                                    </dd>
                                </div>
                                @endif

                                {{-- TODO: Add Admin Edit Button conditionally --}}

                            </dl>
                        </div>
                    </div> {{-- End Details Column --}}
                </div>
            </x-ui.card>


            {{-- Related Companies (Subsidiaries) Section --}}
            @if($group->companies->isNotEmpty()) {{-- Use the relationship --}}
            <section>
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Subsidiaries & Associated Companies</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($group->companies as $company) {{-- Loop through the relationship --}}
                    {{-- Include Company Card Partial/Component --}}
                    @include('partials._company_card', ['company' => $company])
                    @endforeach
                </div>
                {{-- Optionally add link to view all companies filtered by this group --}}
                {{-- @if($group->companies()->count() > 6) Link to companies.index?group=... @endif --}}
            </section>
            @endif

            {{-- TODO: Add Related People/Orgs if using polymorphic 'members' relationship --}}


            {{-- Comments Section (assuming Groups are commentable) --}}
            <div class="mt-12 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    @include('partials._comment_section', [
                    'commentable' => $group,
                    'type' => 'group',
                    'comments' => $comments, /* Pass comments fetched in controller */
                    'currentSort' => $currentSort
                    ])
                </div>
            </div>

        </div>
    </div>
    @push('scripts') @endpush {{-- For comment reply JS --}}
</x-app-layout>