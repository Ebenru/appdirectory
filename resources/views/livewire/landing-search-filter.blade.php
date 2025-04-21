{{-- resources/views/livewire/landing-search-filter.blade.php --}}
<div class="container px-4 md:px-6">
    <div class="mx-auto max-w-3xl space-y-6 text-center">
        <h2 class="text-3xl font-bold tracking-tighter sm:text-4xl md:text-5xl">
            Find Contributors & Organizations
        </h2>
        <p class="text-muted-foreground md:text-xl">
            Search our directory or filter by type below.
        </p>

        <form wire:submit.prevent="performSearch">
            {{-- Row 1: Search Input & Submit Button --}}
            <div class="flex flex-col sm:flex-row gap-4 mb-4">
                <div class="relative flex-grow">
                    <x-lucide-search class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <x-ui.input wire:model.lazy="searchQuery" type="search" name="query" placeholder="Search by name, title, keyword..." class="w-full pl-8 h-10" />
                </div>
                <x-ui.button type="submit" size="lg" class="h-10">Search</x-ui.button>
            </div>

            {{-- Row 2: Entity Type Tags (Always Visible) --}}
            <div class="border-t dark:border-gray-700 pt-4 text-left">
                <div class="flex flex-wrap justify-center gap-2">
                    {{-- Loop through $availableEntities --}}
                    @foreach($availableEntities as $slug => $name)
                    @php
                    // Check if current tag is selected
                    $isSelected = in_array($slug, $selectedEntities);
                    @endphp
                    <button
                        type="button"
                        wire:click="toggleEntityType('{{ $slug }}')"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2
                            {{ $isSelected
                                ? 'bg-primary border-transparent text-primary-foreground hover:bg-primary/80'
                                : 'border-input bg-background hover:bg-accent hover:text-accent-foreground'
                            }}">
                        {{ $name }}
                        {{-- Optional: Add checkmark if selected --}}
                        {{-- @if($isSelected) <x-lucide-check class="ml-1.5 h-3 w-3"/> @endif --}}
                    </button>
                    @endforeach
                </div>
                {{-- Clear button - only show if filters are active --}}
                @if(!empty($selectedEntities) || trim($searchQuery))
                <div class="text-center mt-4">
                    <button type="button" wire:click="clearAllFilters" class="text-xs text-muted-foreground hover:text-primary underline">Clear Filters</button>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>