<x-app-layout>

    {{-- 1. Hero Section --}}
    <section class="bg-gradient-to-br from-red-50 via-red-100 to-red-200 dark:from-gray-900 dark:via-red-900/30 dark:to-gray-900 py-20 sm:py-28 text-center">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-extrabold tracking-tight text-primary sm:text-5xl lg:text-6xl drop-shadow-sm">
                Track and support global boycotts for the causes that matter.
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-700 dark:text-gray-300 max-w-2xl mx-auto">
                Take action today! Help us uncover and boycott Worldwide Criminels.
            <p class="text-red-600"> Discover, search, and share your suggestions to make a difference! </p>
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <x-ui.button size="lg" as-child>
                    <a href="{{ route('search.index') }}">Explore Contributors</a>
                </x-ui.button>
                <a href="#submit" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100 hover:text-primary">
                    Nominate Someone <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

    {{-- === ADD LIVEWIRE COMPONENT === --}}
    <section class="w-full py-12 md:py-16 bg-gray-50 dark:bg-gray-800"> {{-- Keep the section wrapper --}}
        <livewire:landing-search-filter
            :peopleCategories="$peopleCategories"
            :companyCategories="$companyCategories" />
    </section>
    {{-- === END LIVEWIRE COMPONENT === --}}


    {{-- 2. Categories Section --}}
    <section class="py-16 sm:py-24 bg-white dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold tracking-tight text-center text-gray-900 dark:text-white mb-12">
                Explore by Contribution Area
            </h2>
            @if (!empty($categories))
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                {{-- The CategoryCard component loop remains the same --}}
                @foreach ($categories as $category)
                <x-category-card
                    :name="$category['name']"
                    :count="$category['count']"
                    :icon="$category['icon']"
                    :slug="$category['slug']"
                    :type="$category['type']" />
                @endforeach
            </div>
            @else
            <p class="text-center text-muted-foreground">Categories will appear here once contributors are approved.</p>
            @endif
        </div>
    </section>

    {{-- 3. "How it Works" / About Section --}}
    <section class="py-16 sm:py-24 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold tracking-tight text-center text-gray-900 dark:text-white mb-16">
                Shining a Light on Adherence
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                {{-- Step 1 --}}
                <div>
                    <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary text-primary-foreground shadow-lg">
                        <x-lucide-mouse-pointer-click class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Submit a Nomination</h3>
                    <p class="text-muted-foreground">
                        Know someone or an organization that championed our cause? Use our simple form to share their story and contribution.
                    </p>
                </div>
                {{-- Step 2 --}}
                <div>
                    <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary text-primary-foreground shadow-lg">
                        <x-lucide-clipboard-check class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Admin Review</h3>
                    <p class="text-muted-foreground">
                        Our team reviews each submission to ensure it aligns with the campaign's spirit and acknowledges genuine adherence.
                    </p>
                </div>
                {{-- Step 3 --}}
                <div>
                    <div class="mb-4 inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary text-primary-foreground shadow-lg">
                        <x-lucide-party-popper class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Celebrate!</h3>
                    <p class="text-muted-foreground">
                        Once approved, the contributor's profile is added to the directory, celebrating their commitment for all to see!
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Featured/Recent Contributors Section (Optional - Placeholder) --}}
    {{-- To implement this, you'd fetch recent approved People/Companies in the controller --}}
    {{-- and loop through them here, perhaps using a smaller card style --}}
    {{-- <section class="py-16 sm:py-24 bg-white dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
             <h2 class="text-3xl font-bold tracking-tight text-center text-gray-900 dark:text-white mb-12">
                Recently Added Champions
            </h2>
             <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                 <!-- Loop through $recentContributors -->
                 <!-- Example Card -->
                 <x-ui.card>
                     <x-ui.card-header class="flex flex-row items-center gap-4">
                         <img class="h-10 w-10 rounded-full" src="..." alt="">
                         <div>
                             <x-ui.card-title>Contributor Name</x-ui.card-title>
                             <x-ui.card-description>Contributor Title</x-ui.card-description>
                         </div>
                     </x-ui.card-header>
                     <x-ui.card-content>
                         <p class="text-sm text-muted-foreground line-clamp-2">Short description...</p>
                     </x-ui.card-content>
                 </x-ui.card>
                 <!-- End Loop -->
             </div>
        </div>
    </section> --}}


    {{-- 5. Final Call to Action (CTA) Section --}}
    <section class="py-16 sm:py-24 bg-gradient-to-tr from-red-600 to-red-800 dark:from-red-800 dark:to-red-950" id="submit">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Help Us Recognise Everyone
            </h2>
            <p class="mt-4 text-lg leading-8 text-red-100 max-w-xl mx-auto">
                Your nomination ensures we don't miss anyone deserving of recognition. Let's celebrate every act of adherence together!
            </p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-6">
                <x-ui.button size="lg" variant="secondary" as-child> {{-- Secondary or custom white/light variant --}}
                    <a href="{{ route('people.create') }}">Submit a Person</a>
                </x-ui.button>
                <x-ui.button size="lg" variant="secondary" as-child>
                    <a href="{{ route('companies.create') }}">Submit an Organization</a>
                </x-ui.button>
                @guest
                <x-ui.button size="lg" variant="outline" class="border-white text-white hover:bg-white/10" as-child>
                    <a href="{{ route('register') }}">Register to Contribute</a>
                </x-ui.button>
                @endguest
            </div>
        </div>
    </section>

</x-app-layout>