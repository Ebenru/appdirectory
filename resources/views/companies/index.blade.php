<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Honouring Our Partner Organizations
            </h2>
            {{-- Category Filter Dropdown --}}
            <form method="GET" action="{{ route('companies.index') }}" class="flex items-center space-x-2">
                <div class="relative flex-grow sm:flex-grow-0">
                    <x-lucide-search class="absolute left-2 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <x-ui.input
                        type="search"
                        name="search"
                        placeholder="Search name, description..."
                        class="pl-8 w-full sm:w-48 md:w-64"
                        value="{{ $searchQuery ?? '' }}" {{-- Display current search query --}} />
                </div>
                <select name="category" onchange="this.form.submit()" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $slug => $name)
                    <option value="{{ $slug }}" @selected($selectedCategory==$slug)>
                        {{ $name }}
                    </option>
                    @endforeach
                </select>
                @if($selectedCategory)
                <a href="{{ route('companies.index') }}" class="text-sm text-muted-foreground hover:text-primary">Clear</a>
                @endif
            </form>
        </div>
    </x-slot>

    @if ($companies->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($companies as $company)
        <x-ui.card class="overflow-hidden transition-all hover:shadow-lg">
            <a href="{{ route('companies.show', $company) }}" class="block">
                @if($company->display_logo_url)
                <div class="w-full h-48 bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-4">
                    <img src="{{ $company->display_logo_url }}" alt="{{ $company->displayName ?? $company->legalName }} Logo" class="max-w-full max-h-full object-contain">
                </div>
                @else
                {{-- Placeholder Logo --}}
                <div class="w-full h-48 bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center">
                    <x-lucide-building-2 class="w-16 h-16 text-red-400" />
                </div>
                @endif

                <x-ui.card-header>

                    <div class="flex justify-between items-center">
                        <x-ui.badge class="mb-1 w-fit"> {{-- Added w-fit to make it wrap content --}}
                            {{ $company->category_display_name }}
                        </x-ui.badge>
                        @auth
                        <form method="POST" action="{{ route('likes.toggle', ['type' => 'company', 'id' => $company->id]) }}">
                            @csrf
                            <x-ui.button type="submit" variant="{{ Auth::user()->hasLiked($company) ? 'destructive' : 'outline' }}" size="sm" class="flex items-center gap-1">
                                @if(Auth::user()->hasLiked($company))
                                <x-lucide-heart-crack class="w-4 h-4" /> Liked
                                @else
                                <x-lucide-heart class="w-4 h-4" /> Like
                                @endif
                                <span>({{ $company->like_count }})</span>
                            </x-ui.button>
                        </form>
                        @else
                        <span class="text-sm text-muted-foreground flex items-center gap-1">
                            <x-lucide-heart class="w-4 h-4" /> {{ $company->like_count }}
                        </span>
                        @endauth
                    </div>

                    <x-ui.card-title class="text-lg">{{ $company->displayName ?? $company->legalName }}</x-ui.card-title>
                    @if($company->displayName && $company->displayName != $company->legalName)
                    <x-ui.card-description>Legal: {{ $company->legalName }}</x-ui.card-description>
                    @endif
                </x-ui.card-header>
                <x-ui.card-content>
                    <p class="text-sm text-muted-foreground line-clamp-3">
                        {{ $company->description }}
                    </p>
                </x-ui.card-content>

                {{-- Optional: If you wanted a footer, add <x-ui.card-footer> here --}}

                {{-- Optional: If you wanted to add a button, you can do so here --}}
            </a>
        </x-ui.card>
        @endforeach
    </div>

    {{-- Pagination Links --}}
    <div class="mt-8">
        {{ $companies->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <x-lucide-search class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No Organizations Found</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            @if($selectedCategory)
            No organizations found in the category "{{ $categories[$selectedCategory] ?? $selectedCategory }}".
            <a href="{{ route('companies.index') }}" class="text-primary hover:underline">View all categories</a>.
            @else
            We couldn't find any approved organizations in the directory yet.
            @endif
        </p>
        @auth
        <div class="mt-6">
            <x-ui.button as-child>
                <a href="{{ route('companies.create') }}">Submit an Organization</a>
            </x-ui.button>
        </div>
        @endauth
    </div>
    @endif

</x-app-layout>