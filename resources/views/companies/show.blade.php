<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $company->displayName ?? $company->legalName }}
        </h2>
    </x-slot>

    {{-- Main content container for padding --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> {{-- Added padding here --}}

            {{-- Card for Company Details --}}
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex flex-col md:flex-row gap-6">
                    {{-- Logo Column --}}
                    <div class="flex-shrink-0 md:w-1/3 lg:w-1/4 flex items-center justify-center">
                        {{-- Use the display_logo_url accessor --}}
                        <div class="h-48 w-full p-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md">
                            <img class="max-h-full max-w-full object-contain" src="{{ $company->display_logo_url }}" alt="{{ $company->displayName ?? $company->legalName }}">
                        </div>
                        {{-- Removed redundant placeholder div --}}
                    </div>

                    {{-- Details Column --}}
                    <div class="flex-grow">
                        {{-- Header Row with Like Button --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-2xl font-semibold leading-7 text-gray-900 dark:text-white">{{ $company->displayName ?? $company->legalName }}</h3>
                                @if($company->displayName && $company->displayName !== $company->legalName)
                                <p class="mt-1 max-w-2xl text-md leading-6 text-gray-500 dark:text-gray-400">Legal Name: {{ $company->legalName }}</p>
                                @endif
                                @if($company->website_url)
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-primary hover:underline">
                                    <a href="{{ $company->website_url }}" target="_blank" rel="noopener noreferrer">
                                        Visit Website <x-lucide-external-link class="inline-block h-3 w-3 ml-1" />
                                    </a>
                                </p>
                                @endif
                            </div>
                            {{-- Include Like Button partial --}}
                            @include('partials._like_button', ['likeable' => $company, 'type' => 'company'])
                        </div>

                        {{-- Details List (dl) --}}
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Category --}}
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <a href="{{ route('companies.index', ['category' => $company->cmpCategory]) }}" class="text-primary hover:underline">
                                            <x-dynamic-component :component="'lucide-' . strtolower($company->category_icon_name)" class="inline-block h-4 w-4 mr-1" />
                                            {{ $company->category_display_name }}
                                        </a>
                                    </dd>
                                </div>

                                {{-- Parent Group --}}
                                @if($company->group)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Parent Group</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        <a href="{{ route('groups.show', $company->group) }}" class="text-primary hover:underline">
                                            {{ $company->group->name }}
                                        </a>
                                    </dd>
                                </div>
                                @endif

                                {{-- Country --}}
                                @if($company->country)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country (HQ)</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <img>
                                        <a href="{{ route('countries.show', $company->country) }}" class="text-primary hover:underline">
                                            <img src="{{ $company->country->flag_icon_url }}" alt="{{ $company->country->name }} Flag" class="inline-block h-4 w-4 mr-1  rounded-full">
                                            {{ $company->country->name }}
                                        </a>
                                    </dd>
                                </div>
                                @endif

                                {{-- About --}}
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">About</dt>
                                    {{-- Removed whitespace-pre-wrap class --}}
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        {{-- Kept trim() for safety --}}
                                        {{ trim($company->description) ?? 'No description provided.' }}
                                    </dd>
                                </div>

                                {{-- Display Key Achievements --}}
                                @if($company->key_achievements)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Key Achievements</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $company->key_achievements }}</dd>
                                </div>
                                @endif

                                {{-- Display Featured Article Link --}}
                                @if($company->featured_article_url)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Featured Link</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <a href="{{ $company->featured_article_url }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline break-all">
                                            {{ $company->featured_article_url }} <x-lucide-external-link class="inline-block h-3 w-3 ml-1" />
                                        </a>
                                    </dd>
                                </div>
                                @endif


                                {{-- Tags --}}
                                @if($company->tags->isNotEmpty())
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($company->tags as $tag)
                                            <x-ui.badge variant="secondary">
                                                {{-- TODO: Link to tag search page later --}}
                                                {{ $tag->name }}
                                            </x-ui.badge>
                                            @endforeach
                                        </div>
                                    </dd>
                                </div>
                                @endif

                                {{-- Social Media --}}
                                @if(!empty($company->social_media))
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Social Links</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <div class="flex flex-wrap gap-4">
                                            @foreach($company->social_media as $platform => $url)
                                            @if($url)
                                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary" title="{{ ucfirst($platform) }}">
                                                {{-- Ensure you have blade-lucide-icons installed --}}
                                                <x-dynamic-component :component="'lucide-' . strtolower($platform)" class="w-5 h-5" />
                                            </a>
                                            @endif
                                            @endforeach
                                        </div>
                                    </dd>
                                </div>
                                @endif

                                {{-- Sources --}}
                                @if(!empty($company->sources))
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sources</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($company->sources as $sourceUrl)
                                            <li>
                                                <a href="{{ $sourceUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline break-all" title="{{ $sourceUrl }}">
                                                    {{ Str::limit($sourceUrl, 50) }} {{-- Limit display length --}}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </dd>
                                </div>
                                @endif

                                {{-- Submitted --}}
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</dt>
                                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:mt-0 sm:col-span-2">
                                        {{ $company->created_at->format('M d, Y') }}
                                        @if($company->submittedBy)
                                        by {{ $company->submittedBy->name }}
                                        @endif
                                    </dd>
                                </div>

                                {{-- Approved --}}
                                @if($company->approved_at)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</dt>
                                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:mt-0 sm:col-span-2">
                                        {{ $company->approved_at->format('M d, Y') }}
                                        @if($company->approvedBy)
                                        by {{ $company->approvedBy->name }}
                                        @endif
                                    </dd>
                                </div>
                                @endif

                                {{-- Edit Button (Show conditionally) --}}
                                @can('update', $company)
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Actions</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                        <x-ui.button variant="outline" size="sm" as-child>
                                            <a href="{{ route('companies.edit', $company) }}">Edit Submission</a>
                                        </x-ui.button>
                                    </dd>
                                </div>
                                @endcan


                            </dl>
                        </div> {{-- End Details List --}}
                    </div> {{-- End Details Column --}}
                </div> {{-- End Main Flex Row --}}
            </div> {{-- End Card --}}

            {{-- Comments Section --}}
            <div class="mt-12 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6"> {{-- Add padding for comments section --}}
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Discussion ({{ $company->comments()->count() + $company->comments()->withCount('replies')->get()->sum('replies_count') }})
                        </h2>
                        <div class="text-sm flex space-x-3">
                            <span class="text-muted-foreground">Sort by:</span>
                            <a href="{{ url()->current() }}?sort=newest" class="{{ $currentSort == 'newest' ? 'text-primary font-medium' : 'text-muted-foreground hover:text-primary' }}">Newest</a>
                            <a href="{{ url()->current() }}?sort=likes" class="{{ $currentSort == 'likes' ? 'text-primary font-medium' : 'text-muted-foreground hover:text-primary' }}">Most Liked</a>
                        </div>
                    </div>

                    {{-- Top-Level Comment Form --}}
                    @auth
                    @include('partials._comment_form', [
                    'commentable_id' => $company->id,
                    'commentable_type' => 'company' // Pass type as string
                    ])
                    @else
                    <p class="text-center text-muted-foreground py-4">
                        <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="text-primary hover:underline">Log in</a> to join the discussion.
                    </p>
                    @endauth

                    {{-- Display Comments --}}
                    <div class="mt-8 space-y-6">
                        {{-- $comments should be passed already sorted from controller --}}
                        @forelse($comments as $comment)
                        @include('partials._comment', ['comment' => $comment])
                        @empty
                        <p class="text-center text-muted-foreground pt-4">Be the first to comment!</p>
                        @endforelse
                    </div>
                </div>
            </div> {{-- End Comments Card --}}

        </div> {{-- End Max Width Container --}}
    </div> {{-- End Outer Padding Div --}}

    @push('scripts') @endpush {{-- For comment reply JS --}}
</x-app-layout>