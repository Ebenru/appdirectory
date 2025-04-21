<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Celebrate Adherence</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

</head>

<body class="font-sans antialiased bg-background text-foreground">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        {{-- Use navigation partial provided by Breeze --}}
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- Flash Messages --}}
                @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
                @endif
                @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                @endif

                {{ $slot }} {{-- Main content slot --}}
            </div>
        </main>

        {{-- New Footer Section --}}
        <footer class="py-16 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 mt-auto"> {{-- Add mt-auto if needed --}}
            <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {{-- Footer Links Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
                    {{-- Directory Column --}}
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-gray-900 dark:text-white mb-4">Directory</h3>
                        <ul role="list" class="space-y-3">
                            <li><a href="{{ route('people.index') }}" class="text-sm leading-6 text-muted-foreground hover:text-primary">All People</a></li>
                            <li><a href="{{ route('companies.index') }}" class="text-sm leading-6 text-muted-foreground hover:text-primary">All Organizations</a></li>
                            <li><a href="{{ route('landing') }}#categories" class="text-sm leading-6 text-muted-foreground hover:text-primary">Categories</a></li>
                            {{-- <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Featured</a></li>  --}}
                            <li><a href="{{ route('people.create') }}" class="text-sm leading-6 text-muted-foreground hover:text-primary">Submit Entry</a></li>
                        </ul>
                    </div>
                    {{-- Blog Column (Placeholder) --}}
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-gray-900 dark:text-white mb-4">Blog</h3>
                        <ul role="list" class="space-y-3">
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">All Articles</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Interviews</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Insights</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Stories</a></li>
                        </ul>
                    </div>
                    {{-- About Column (Placeholder) --}}
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-gray-900 dark:text-white mb-4">About</h3>
                        <ul role="list" class="space-y-3">
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Our Mission</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Team</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Selection Criteria</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Contact Us</a></li>
                        </ul>
                    </div>
                    {{-- Legal Column (Placeholder) --}}
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-gray-900 dark:text-white mb-4">Legal</h3>
                        <ul role="list" class="space-y-3">
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Privacy Policy</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Terms of Service</a></li>
                            <li><a href="#" class="text-sm leading-6 text-muted-foreground hover:text-primary">Cookie Policy</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Bottom Bar --}}
                <div class="mt-10 pt-8 border-t border-gray-300 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between">
                    <div class="flex items-center justify-center sm:justify-start space-x-3 mb-4 sm:mb-0">
                        {{-- Placeholder Logo --}}
                        <a href="{{ route('landing') }}" class="flex items-center space-x-2">
                            <x-lucide-award class="h-6 w-6 text-primary" /> {{-- Example icon --}}
                            <span class="font-semibold text-gray-900 dark:text-white">{{ config('app.name', 'ContributorHub') }}</span>
                        </a>
                    </div>
                    <p class="text-xs leading-5 text-muted-foreground text-center sm:text-left">
                        © {{ date('Y') }} {{ config('app.name', 'ContributorHub') }}. All rights reserved.
                    </p>
                    <div class="flex justify-center sm:justify-end space-x-5 mt-4 sm:mt-0">
                        <a href="#" class="text-muted-foreground hover:text-primary">
                            <span class="sr-only">Twitter</span>
                            <x-lucide-twitter class="h-5 w-5" />
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary">
                            <span class="sr-only">Instagram</span>
                            <x-lucide-instagram class="h-5 w-5" />
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary">
                            <span class="sr-only">LinkedIn</span>
                            <x-lucide-linkedin class="h-5 w-5" />
                        </a>
                    </div>
                </div>
            </div>
        </footer>
        {{-- End Footer Section --}}
    </div>

    @livewireScripts

    @stack('scripts')
</body>

</html>