{{-- resources/views/events/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Event: {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Main Details Card --}}
            <x-ui.card>
                {{-- Featured Image Header --}}
                <div class="w-full h-64 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden sm:rounded-t-lg">
                    <img src="{{ $event->display_featured_image_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover">
                </div>

                <div class="p-4 sm:p-6">
                    {{-- Details Column --}}
                    <div class="flex-grow">
                        {{-- Header Row with Like Button --}}
                        <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-4 mb-4">
                            <div>
                                <h3 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-white">{{ $event->name }}</h3>
                                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted-foreground">
                                    <span>
                                        <x-lucide-calendar-days class="inline-block w-4 h-4 mr-1" />
                                        {{ $event->startDate->format('D, M j, Y') }}
                                        @if($event->endDate && $event->endDate->ne($event->startDate))
                                        - {{ $event->endDate->format('D, M j, Y') }}
                                        @endif
                                    </span>
                                    @if($event->location)
                                    <span>
                                        <x-lucide-map-pin class="inline-block w-4 h-4 mr-1" /> {{ $event->location }}
                                        @if($event->is_virtual) (Also Virtual) @endif
                                    </span>
                                    @elseif($event->is_virtual)
                                    <span><x-lucide-laptop class="inline-block w-4 h-4 mr-1" /> Virtual Event</span>
                                    @endif
                                    @if($event->eventType)
                                    <span><x-lucide-info class="inline-block w-4 h-4 mr-1" /> {{ $event->eventType }}</span>
                                    @endif
                                </div>
                                @if($event->website_url)
                                <p class="mt-3 max-w-2xl text-sm leading-6 text-primary hover:underline">
                                    <a href="{{ $event->website_url }}" target="_blank" rel="noopener noreferrer">
                                        Visit Event Website <x-lucide-external-link class="inline-block h-3 w-3 ml-1" />
                                    </a>
                                </p>
                                @endif
                            </div>
                            {{-- Include Like Button partial (assuming Events are likeable) --}}
                            @include('partials._like_button', ['likeable' => $event, 'type' => 'event'])
                        </div>

                        {{-- Details List (dl) --}}
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">

                                {{-- Description --}}
                                @if($event->description)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">About this Event</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $event->description }}</dd>
                                </div>
                                @endif

                                {{-- Country --}}
                                @if($event->country)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Country</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        <a href="{{ route('countries.show', $event->country) }}" class="text-primary hover:underline">
                                            {{ $event->country->name }}
                                        </a>
                                    </dd>
                                </div>
                                @endif

                                {{-- Key Achievements --}}
                                @if($event->key_achievements)
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Key Outcomes/Info</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2 whitespace-pre-wrap">{{ $event->key_achievements }}</dd>
                                </div>
                                @endif

                                {{-- Tags, Social, Sources, Featured Article... --}}
                                @include('partials._show_common_attributes', ['entity' => $event])


                                {{-- TODO: Add Organizers / Sponsors section based on relationships --}}

                                {{-- TODO: Add Admin Edit Button conditionally --}}

                            </dl>
                        </div>
                    </div> {{-- End Details Column --}}
                </div>
            </x-ui.card>


            {{-- Comments Section (assuming Events are commentable) --}}
            <div class="mt-12 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    @include('partials._comment_section', [
                    'commentable' => $event,
                    'type' => 'event',
                    'comments' => $comments, /* Pass comments from controller */
                    'currentSort' => $currentSort
                    ])
                </div>
            </div>

        </div>
    </div>
    @push('scripts') @endpush {{-- For comment reply JS --}}
</x-app-layout>