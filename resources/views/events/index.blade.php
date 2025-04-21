{{-- resources/views/events/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Browse Events & Campaigns
        </h2>
        {{-- TODO: Add Search/Filter bar specific to Events if needed --}}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Upcoming & Recent Events</h3>
                    @if($events->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($events as $event)
                        <a href="{{ route('events.show', $event) }}" class="block group">
                            <x-ui.card class="overflow-hidden transition-all group-hover:shadow-lg h-full flex flex-col">
                                {{-- Featured Image --}}
                                <div class="w-full h-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $event->display_featured_image_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                </div>
                                <x-ui.card-header class="flex-grow">
                                    <p class="text-xs text-muted-foreground">
                                        {{ $event->startDate->format('M d, Y') }}
                                        @if($event->endDate && $event->endDate->ne($event->startDate))
                                        - {{ $event->endDate->format('M d, Y') }}
                                        @endif
                                        @if($event->eventType) | {{ $event->eventType }} @endif
                                    </p>
                                    <x-ui.card-title class="text-base mt-1 group-hover:text-primary">{{ $event->name }}</x-ui.card-title>
                                </x-ui.card-header>
                                <x-ui.card-footer class="text-xs text-muted-foreground">
                                    @if($event->location) Location: {{ Str::limit($event->location, 30) }} @endif
                                </x-ui.card-footer>
                            </x-ui.card>
                        </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $events->links() }}
                    </div>
                    @else
                    <p class="text-muted-foreground">No events found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>