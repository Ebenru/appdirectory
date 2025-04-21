{{-- resources/views/partials/_company_card.blade.php --}}
@props(['company'])

<x-ui.card class="overflow-hidden transition-all hover:shadow-lg h-full flex flex-col">
    <a href="{{ route('companies.show', $company) }}" class="block flex flex-col flex-grow">
        <div class="w-full h-48 bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-4">
            <img src="{{ $company->display_logo_url }}" alt="{{ $company->displayName ?? $company->legalName }} Logo" class="max-w-full max-h-full object-contain">
        </div>
        <x-ui.card-header class="flex-grow">
            <x-ui.card-title class="text-lg">{{ $company->displayName ?? $company->legalName }}</x-ui.card-title>
            @if($company->displayName && $company->displayName != $company->legalName)
            <x-ui.card-description>Legal: {{ $company->legalName }}</x-ui.card-description>
            @endif
        </x-ui.card-header>
        {{-- No description on card for brevity? Or limit it --}}
        {{-- <x-ui.card-content class="min-h-[40px] flex-grow-0">
             <p class="text-sm text-muted-foreground line-clamp-2">{{ Str::limit($company->description, 80) }}</p>
        </x-ui.card-content> --}}
        <x-ui.card-footer class="text-xs text-muted-foreground pt-3 pb-3 border-t dark:border-gray-700 flex justify-between items-center">
            <span>Cat: {{ $company->category_display_name }}</span>
            {{-- @include('partials._like_button', ['likeable' => $company, 'type' => 'company']) --}}
        </x-ui.card-footer>
    </a>
</x-ui.card>