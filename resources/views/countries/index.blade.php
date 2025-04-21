{{-- resources/views/countries/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Browse by Country
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Countries with Contributors</h3>
                    @if($countries->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($countries as $country)
                        <a href="{{ route('countries.show', $country) }}" class="block p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-700 transition-colors">
                            {{-- Display Flag Icon if URL exists --}}
                            {{-- @if($country->flag_icon_url)
                                         <img src="{{ $country->flag_icon_url }}" class="inline-block h-4 w-auto mr-2" alt="">
                            @endif --}}
                            <span class="font-medium text-primary">{{ $country->name }}</span>
                            <span class="text-sm text-muted-foreground ml-1">({{ $country->iso_code }})</span>
                            {{-- Optionally display counts if loaded --}}
                            {{-- <p class="text-xs text-muted-foreground mt-1">
                                         {{ $country->people_count ?? '?' }} People / {{ $country->companies_count ?? '?' }} Orgs
                            </p> --}}
                        </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $countries->links() }}
                    </div>
                    @else
                    <p class="text-muted-foreground">No countries found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>