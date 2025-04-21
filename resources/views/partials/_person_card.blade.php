{{-- resources/views/partials/_person_card.blade.php --}}
@props(['person'])

<x-ui.card class="overflow-hidden transition-all hover:shadow-lg h-full flex flex-col"> {{-- Ensure full height for grid alignment --}}
    <a href="{{ route('people.show', $person) }}" class="block flex flex-col flex-grow">
        <img src="{{ $person->display_photo_url }}" alt="{{ $person->fullName }}" class="w-full h-48 object-cover">

        <x-ui.card-header class="flex-grow">
            <x-ui.card-title class="text-lg">{{ $person->fullName }}</x-ui.card-title>
            @if($person->title)
            <x-ui.card-description>{{ $person->title }}</x-ui.card-description>
            @endif
        </x-ui.card-header>
        <x-ui.card-content class="min-h-[40px] flex-grow-0"> {{-- Adjust min-height as needed --}}
            <p class="text-sm text-muted-foreground line-clamp-2">
                {{ Str::limit($person->description, 80) }} {{-- Limit description --}}
            </p>
        </x-ui.card-content>
        <x-ui.card-footer class="text-xs text-muted-foreground pt-3 pb-3 border-t dark:border-gray-700 flex justify-between items-center">
            <span>Cat: {{ $person->category_display_name }}</span>
            {{-- Add like button if desired on cards --}}
            {{-- @include('partials._like_button', ['likeable' => $person, 'type' => 'person']) --}}
        </x-ui.card-footer>
    </a>
</x-ui.card>