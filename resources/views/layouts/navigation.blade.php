<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    {{-- Link to Landing page instead of dashboard if dashboard isn't used --}}
                    <a href="{{ route('landing') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Example link to Landing Page, adjust as needed --}}
                    <x-nav-link :href="route('landing')" :active="request()->routeIs('landing')">
                        {{ __('Home') }} {{-- Changed from Dashboard --}}
                    </x-nav-link>
                    {{-- Add other primary navigation links here --}}
                    <x-nav-link :href="route('people.index')" :active="request()->routeIs('people.index')">
                        {{ __('People') }}
                    </x-nav-link>
                    <x-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.index')">
                        {{ __('Companies') }}
                    </x-nav-link>
                    <x-nav-link :href="route('countries.index')" :active="request()->routeIs('countries.index')">
                        {{ __('Countries') }}
                    </x-nav-link>
                    {{-- ADDED Group Link --}}
                    <x-nav-link :href="route('groups.index')" :active="request()->routeIs('groups.*')">
                        {{ __('Groups') }}
                    </x-nav-link>
                    {{-- ADDED Organizations Link --}}
                    <x-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')">
                        {{ __('Organizations') }}
                    </x-nav-link>
                    {{-- ADDED Events Link --}}
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                        {{ __('Events') }}
                    </x-nav-link>
                    {{-- Admin Links --}}
                    @if(Auth::check() && Auth::user()->is_admin)
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Admin Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.pending')" :active="request()->routeIs('admin.pending')">
                        {{ __('Pending') }}
                        @php // Calculate total pending count for badge
                        $totalPending = (isset($pendingPeopleCount) ? $pendingPeopleCount : \App\Models\Person::where('status', 'pending')->count())
                        + (isset($pendingCompanyCount) ? $pendingCompanyCount : \App\Models\Company::where('status', 'pending')->count());
                        @endphp
                        @if($totalPending > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $totalPending }}
                        </span>
                        @endif
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown / Guest Links -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth {{-- START: Show only if user is logged in --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            {{-- Now safe to access name --}}
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('My Profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('My Dashboard') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('my.submissions')" :active="request()->routeIs('my.submissions')">
                            {{ __('My Submissions') }}
                        </x-dropdown-link>

                        <!-- {{-- Add Admin Links Conditionally --}}
                        @if(Auth::user()->is_admin)
                        <x-dropdown-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin Dashboard') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('admin.pending')" :active="request()->routeIs('admin.pending')">
                            {{ __('Pending Approvals') }}
                            @php // Calculate total pending count for badge
                            $totalPending = (isset($pendingPeopleCount) ? $pendingPeopleCount : \App\Models\Person::where('status', 'pending')->count())
                            + (isset($pendingCompanyCount) ? $pendingCompanyCount : \App\Models\Company::where('status', 'pending')->count());
                            @endphp
                            @if($totalPending > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $totalPending }}
                            </span>
                            @endif
                        </x-dropdown-link>
                        @endif -->

                        {{-- Add other dropdown links here --}}

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else {{-- START: Show only if user is a guest --}}
                <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                @endif
                @endauth {{-- END: Auth/Guest check for desktop --}}
            </div>


            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Example link to Landing Page, adjust as needed --}}
            <x-responsive-nav-link :href="route('landing')" :active="request()->routeIs('landing')">
                {{ __('Home') }} {{-- Changed from Dashboard --}}
            </x-responsive-nav-link>
            {{-- Add other responsive navigation links here --}}
            <x-responsive-nav-link :href="route('people.index')" :active="request()->routeIs('people.index')">
                {{ __('People') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.index')">
                {{ __('Companies') }}
            </x-responsive-nav-link>

            {{-- Add Admin Links Conditionally --}}
            @if(Auth::check() && Auth::user()->is_admin)
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                {{ __('Admin Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.pending')" :active="request()->routeIs('admin.pending')">
                {{ __('Pending Approvals') }}
                @php // Calculate total pending count for badge
                $totalPending = (isset($pendingPeopleCount) ? $pendingPeopleCount : \App\Models\Person::where('status', 'pending')->count())
                + (isset($pendingCompanyCount) ? $pendingCompanyCount : \App\Models\Company::where('status', 'pending')->count());
                @endphp
                @if($totalPending > 0)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $totalPending }}
                </span>
                @endif
            </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth {{-- START: Show only if user is logged in --}}
            <div class="px-4">
                {{-- Now safe to access name and email --}}
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('My Profile') }}
                </x-responsive-nav-link>

                {{-- Add Dashboard Link --}}
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('My Dashboard') }}
                </x-responsive-nav-link>

                {{-- Add My Submissions Link --}}
                <x-responsive-nav-link :href="route('my.submissions')" :active="request()->routeIs('my.submissions')">
                    {{ __('My Submissions') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                            this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>

            @else {{-- START: Show Login/Register if user is a guest --}}
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log In') }}
                </x-responsive-nav-link>
                @if (Route::has('register'))
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
                @endif
            </div>
            @endauth {{-- END: Auth/Guest check for responsive --}}
        </div>
    </div>
</nav>