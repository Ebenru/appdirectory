<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Honouring Our People
            </h2>
            {{-- Category Filter Dropdown --}}
            {{-- Consider styling the select element with Tailwind/UI component for consistency --}}
            <form method="GET" action="{{ route('people.index') }}" class="flex items-center space-x-2 w-full sm:w-auto">
                <div class="relative flex-grow sm:flex-grow-0">
                    <x-lucide-search class="absolute left-2 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <x-ui.input
                        type="search"
                        name="search"
                        placeholder="Search name, title..."
                        class="pl-8 w-full sm:w-48 md:w-64"
                        value="{{ $searchQuery ?? '' }}" {{-- Display current search query --}} />
                </div>
                <select name="category" onchange="this.form.submit()" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"> {{-- Basic Styling Added --}}
                    <option value="">All Categories</option>
                    @foreach($categories as $slug => $name)
                    <option value="{{ $slug }}" @selected($selectedCategory==$slug)>
                        {{ $name }}
                    </option>
                    @endforeach
                </select>
                @if($selectedCategory)
                <a href="{{ route('people.index') }}" class="text-sm text-muted-foreground hover:text-primary">Clear Filter</a> {{-- Added whitespace-nowrap --}}
                @endif
            </form>
        </div>
    </x-slot>



    @if ($people->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($people as $person)
        <x-ui.card class="overflow-hidden transition-all hover:shadow-lg flex flex-col"> {{-- Added flex flex-col --}}
            <a href="{{ route('people.show', $person) }}" class="block flex flex-col flex-grow"> {{-- Added flex flex-col flex-grow --}}
                @if($person->display_photo_url)

                <img src="{{ $person->display_photo_url }}" alt="{{ $person->fullName }}" class="w-full h-48 object-cover" />


                @else
                {{-- Placeholder --}}
                <div class="w-full h-48 bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center flex-shrink-0"> {{-- Added flex-shrink-0 --}}
                    <x-lucide-user class="w-16 h-16 text-muted-foreground" />
                </div>
                @endif

                {{-- Use Card Header for Title and Badge --}}
                <x-ui.card-header>
                    <div class="flex justify-between items-center">
                        {{-- Put Badge before title --}}
                        <x-ui.badge class="mb-1 w-fit"> {{-- Added w-fit to make it wrap content --}}
                            {{ $person->category_display_name }}
                        </x-ui.badge>
                        @auth {{-- Show button only if logged in --}}
                        <form method="POST" action="{{ route('likes.toggle', ['type' => 'person', 'id' => $person->id]) }}">
                            @csrf
                            <x-ui.button type="submit" variant="{{ Auth::user()->hasLiked($person) ? 'destructive' : 'outline' }}" size="sm" class="flex items-center gap-1">
                                @if(Auth::user()->hasLiked($person))
                                <x-lucide-heart-crack class="w-4 h-4" /> Liked
                                @else
                                <x-lucide-heart class="w-4 h-4" /> Like
                                @endif
                                <span>({{ $person->like_count }})</span>
                            </x-ui.button>
                        </form>
                        @else {{-- Show count for non-logged in users --}}
                        <span class="text-sm text-muted-foreground flex items-center gap-1">
                            <x-lucide-heart class="w-4 h-4" /> {{ $person->like_count }}
                        </span>
                        @endauth
                    </div>

                    {{-- Use Card Title for the name --}}
                    <x-ui.card-title class="text-lg">{{ $person->fullName }}</x-ui.card-title>
                    {{-- No description in header for people, unlike companies --}}
                </x-ui.card-header>

                {{-- Use Card Content for the description --}}
                <x-ui.card-content class="flex-grow"> {{-- Added flex-grow --}}
                    @if($person->description)
                    <p class="text-muted-foreground text-sm line-clamp-3">{{ $person->description }}</p>
                    @else
                    <p class="text-muted-foreground text-sm italic">No description available.</p>
                    @endif
                </x-ui.card-content>

                {{-- Optional: If you wanted a footer, add <x-ui.card-footer> here --}}

                {{-- Optional: If you wanted to add a button, you can do so here --}}

            </a> {{-- Close anchor tag --}}

        </x-ui.card> {{-- Close card --}}
        @endforeach
    </div>

    {{-- Pagination Links --}}
    <div class="mt-8">
        {{-- Consider styling pagination using Tailwind for consistency --}}
        {{-- https://laravel.com/docs/10.x/pagination#tailwind-css --}}
        {{ $people->links() }}
    </div>
    @else
    {{-- No People Found Message (Looks fine) --}}
    <div class="text-center py-12">
        <x-lucide-search class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No People Found</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            @if($selectedCategory)
            No contributors found in the category "{{ $categories[$selectedCategory] ?? $selectedCategory }}".
            <a href="{{ route('people.index') }}" class="text-primary hover:underline">View all categories</a>.
            @else
            We couldn't find any approved people in the directory yet.
            @endif
        </p>
        @auth
        <div class="mt-6">
            <x-ui.button as-child>
                <a href="{{ route('people.create') }}">Submit a Person</a>
            </x-ui.button>
        </div>
        @endauth
    </div>
    @endif

    </div> {{-- Close optional max-width container --}}
</x-app-layout>