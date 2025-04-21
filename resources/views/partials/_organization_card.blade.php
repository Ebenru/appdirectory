{{-- resources/views/partials/_organization_card.blade.php --}}
@props(['organization'])

<x-ui.card class="overflow-hidden transition-all hover:shadow-lg h-full flex flex-col">
    <a href="{{ route('organizations.show', $organization) }}" class="block flex flex-col flex-grow">
        <div class="w-full h-48 bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-4">
            <img src="{{ $organization->display_logo_url }}" alt="{{ $organization->name }} Logo" class="max-w-full max-h-full object-contain">
        </div>
        <x-ui.card-header class="flex-grow">
            <x-ui.card-title class="text-lg">{{ $organization->name }}</x-ui.card-title>
            @if($organization->type)
            <x-ui.card-description>{{ $organization->type }}</x-ui.card-description>
            @endif
        </x-ui.card-header>
        {{-- <x-ui.card-content class="min-h-[40px] flex-grow-0">
             <p class="text-sm text-muted-foreground line-clamp-2">{{ Str::limit($organization->description, 80) }}</p>
        </x-ui.card-content> --}}
        <x-ui.card-footer class="text-xs text-muted-foreground pt-3 pb-3 border-t dark:border-gray-700 flex justify-between items-center">
            <span>{{ $organization->scope ?? 'Organization' }}</span>
            {{-- @include('partials._like_button', ['likeable' => $organization, 'type' => 'organization']) --}}
        </x-ui.card-footer>
    </a>
</x-ui.card>