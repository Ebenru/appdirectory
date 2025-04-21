{{-- resources/views/organizations/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Browse Organizations (NGOs, Governing Bodies, etc.)
        </h2>
        {{-- TODO: Add Search/Filter bar specific to Organizations if needed --}}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Featured Organizations</h3>
                    @if($organizations->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($organizations as $organization)
                        <a href="{{ route('organizations.show', $organization) }}" class="block p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-700 transition-colors">
                            <div class="flex items-center space-x-3 mb-2">
                                {{-- Logo --}}
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 dark:bg-gray-700 rounded-md flex items-center justify-center p-1">
                                    <img src="{{ $organization->display_logo_url }}" alt="{{ $organization->name }} Logo" class="max-h-full max-w-full object-contain">
                                </div>
                                <div>
                                    <span class="font-medium text-primary block truncate">{{ $organization->name }}</span>
                                    @if($organization->type)
                                    <p class="text-xs text-muted-foreground">{{ $organization->type }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($organization->description)
                            <p class="text-xs text-muted-foreground line-clamp-2">{{ $organization->description }}</p>
                            @endif
                            {{-- Optionally display country --}}
                            {{-- @if($organization->country)
                                        <p class="text-xs text-muted-foreground mt-1">Based in: {{ $organization->country->name }}</p>
                            @endif --}}
                        </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $organizations->links() }}
                    </div>
                    @else
                    <p class="text-muted-foreground">No organizations found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>