{{-- resources/views/partials/_group_card.blade.php --}}
@props(['group'])

<x-ui.card class="overflow-hidden transition-all hover:shadow-lg h-full flex flex-col">
    <a href="{{ route('groups.show', $group) }}" class="block flex flex-col flex-grow">
        <div class="w-full h-48 bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-4">
            <img src="{{ $group->display_logo_url }}" alt="{{ $group->name }} Logo" class="max-w-full max-h-full object-contain">
        </div>
        <x-ui.card-header class="flex-grow">
            <x-ui.card-title class="text-lg">{{ $group->name }}</x-ui.card-title>
            @if($group->industry)
            <x-ui.card-description>{{ $group->industry }}</x-ui.card-description>
            @endif
        </x-ui.card-header>
        {{-- <x-ui.card-content class="min-h-[40px] flex-grow-0">
            <p class="text-sm text-muted-foreground line-clamp-2">{{ Str::limit($group->description, 80) }}</p>
        </x-ui.card-content> --}}
        <x-ui.card-footer class="text-xs text-muted-foreground pt-3 pb-3 border-t dark:border-gray-700 flex justify-between items-center">
            <span>Group/Holding</span> {{-- Static text or add member count later --}}
            {{-- @include('partials._like_button', ['likeable' => $group, 'type' => 'group']) --}}
        </x-ui.card-footer>
    </a>
</x-ui.card>