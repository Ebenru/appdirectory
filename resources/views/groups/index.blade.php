{{-- resources/views/groups/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Browse Groups & Parent Organizations
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Corporate Groups & Holdings</h3>
                    @if($groups->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($groups as $group)
                        <a href="{{ route('groups.show', $group) }}" class="block p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-700 transition-colors">
                            <div class="flex items-center space-x-3">
                                {{-- Logo --}}
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 dark:bg-gray-700 rounded-md flex items-center justify-center p-1">
                                    <img src="{{ $group->display_logo_url }}" alt="{{ $group->name }} Logo" class="max-h-full max-w-full object-contain">
                                </div>
                                <div>
                                    <span class="font-medium text-primary">{{ $group->name }}</span>
                                    @if($group->industry)
                                    <p class="text-xs text-muted-foreground">{{ $group->industry }}</p>
                                    @endif
                                    {{-- Optionally display counts --}}
                                    {{-- <p class="text-xs text-muted-foreground mt-1">{{ $group->companies_count ?? '?' }} Companies</p> --}}
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $groups->links() }}
                    </div>
                    @else
                    <p class="text-muted-foreground">No groups found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>