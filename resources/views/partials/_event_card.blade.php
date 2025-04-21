{{-- resources/views/partials/_event_card.blade.php --}}
@props(['event'])

<a href="{{ route('events.show', $event) }}" class="block group h-full">
    <x-ui.card class="overflow-hidden transition-all group-hover:shadow-lg h-full flex flex-col">
        {{-- Featured Image --}}
        <div class="w-full h-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
            <img src="{{ $event->display_featured_image_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover transition-transform group-hover:scale-105">
        </div>
        <x-ui.card-header class="flex-grow pb-2">
            <p class="text-xs text-muted-foreground">
                {{ $event->startDate->format('M d, Y') }}
                @if($event->eventType) | {{ $event->eventType }} @endif
            </p>
            <x-ui.card-title class="text-base mt-1 group-hover:text-primary">{{ $event->name }}</x-ui.card-title>
        </x-ui.card-header>
        <x-ui.card-footer class="text-xs text-muted-foreground pt-2 pb-3">
            @if($event->location) Location: {{ Str::limit($event->location, 30) }} @endif
        </x-ui.card-footer>
    </x-ui.card>
</a>