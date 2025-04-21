{{-- Verify/Update content within resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Your Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Welcome Message --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-full object-cover">
                        <div>
                            <h3 class="text-lg font-medium">Welcome back, {{ $user->name }}!</h3>
                            @if($user->bio)
                            <p class="text-sm text-muted-foreground mt-1">{{ Str::limit($user->bio, 100) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                {{-- Total Submissions --}}
                <x-ui.card>
                    <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <x-ui.card-title class="text-sm font-medium">Total Submissions</x-ui.card-title>
                        <x-lucide-send class="h-4 w-4 text-muted-foreground" />
                    </x-ui.card-header>
                    <x-ui.card-content>
                        {{-- Use the passed variable --}}
                        <div class="text-2xl font-bold">{{ $totalSubmissions }}</div>
                        <p class="text-xs text-muted-foreground">People & Organizations Submitted</p>
                    </x-ui.card-content>
                    <x-ui.card-footer>
                        <x-ui.button variant="link" size="sm" class="p-0 h-auto text-xs" as-child>
                            <a href="{{ route('my.submissions') }}">View My Submissions</a>
                        </x-ui.button>
                    </x-ui.card-footer>
                </x-ui.card>

                {{-- Submission Status --}}
                <x-ui.card>
                    <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <x-ui.card-title class="text-sm font-medium">Submission Status</x-ui.card-title>
                        <x-lucide-list-checks class="h-4 w-4 text-muted-foreground" />
                    </x-ui.card-header>
                    <x-ui.card-content>
                        {{-- Use the passed variables --}}
                        <div class="text-lg font-bold">{{ $pendingSubmissions }} Pending</div>
                        <div class="flex justify-between text-xs text-muted-foreground mt-1">
                            <span>{{ $approvedSubmissions }} Approved</span>
                            <span>{{ $rejectedSubmissions }} Rejected</span>
                        </div>
                        {{-- Optional: Progress bar calculation --}}
                        @if($totalSubmissions > 0)
                        @php
                        $approvedPercent = ($approvedSubmissions / $totalSubmissions) * 100;
                        $pendingPercent = ($pendingSubmissions / $totalSubmissions) * 100;
                        $rejectedPercent = ($rejectedSubmissions / $totalSubmissions) * 100;
                        @endphp
                        <div class="mt-3 flex w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden" role="progressbar"
                            aria-valuenow="{{ $approvedPercent }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="flex flex-col justify-center overflow-hidden bg-green-500 text-xs text-white text-center whitespace-nowrap" style="width: {{ $approvedPercent }}%" title="{{ $approvedSubmissions }} Approved"></div>
                            <div class="flex flex-col justify-center overflow-hidden bg-yellow-500 text-xs text-black text-center whitespace-nowrap" style="width: {{ $pendingPercent }}%" title="{{ $pendingSubmissions }} Pending"></div>
                            <div class="flex flex-col justify-center overflow-hidden bg-red-600 text-xs text-white text-center whitespace-nowrap" style="width: {{ $rejectedPercent }}%" title="{{ $rejectedSubmissions }} Rejected"></div>
                        </div>
                        @endif
                    </x-ui.card-content>
                </x-ui.card>

                {{-- Likes Received --}}
                <x-ui.card>
                    <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <x-ui.card-title class="text-sm font-medium">Likes on Your Submissions</x-ui.card-title>
                        <x-lucide-heart class="h-4 w-4 text-muted-foreground" />
                    </x-ui.card-header>
                    <x-ui.card-content>
                        {{-- Use the passed variable --}}
                        <div class="text-2xl font-bold">{{ $totalLikesReceived }}</div>
                        <p class="text-xs text-muted-foreground">On your approved contributions</p>
                    </x-ui.card-content>
                    {{-- Optional Footer Link --}}
                    {{-- <x-ui.card-footer>
                         <a href="#" class="text-xs text-primary hover:underline">View Details</a>
                     </x-ui.card-footer> --}}
                </x-ui.card>
            </div>

            {{-- Notifications Placeholder --}}
            <x-ui.card class="mb-8">
                <x-ui.card-header>
                    <x-ui.card-title>Notifications</x-ui.card-title>
                    <x-ui.card-description>Updates about your submissions or comments.</x-ui.card-description>
                </x-ui.card-header>
                <x-ui.card-content>
                    {{-- Check the passed $notifications variable --}}
                    @if(empty($notifications))
                    <p class="text-muted-foreground text-sm">No new notifications.</p>
                    @else
                    {{-- TODO: Loop through actual notifications here when implemented --}}
                    {{-- Example structure: --}}
                    {{-- <ul class="space-y-3">
                            <li class="text-sm p-3 rounded-md bg-blue-50 dark:bg-blue-900/30">
                                Your submission for <span class="font-medium">"XYZ Corp"</span> has been approved!
                                <span class="block text-xs text-blue-600 dark:text-blue-400">2 hours ago</span>
                            </li>
                            <li class="text-sm p-3 rounded-md bg-yellow-50 dark:bg-yellow-900/30">
                                Your submission for <span class="font-medium">"Jane Doe"</span> requires clarification.
                                <span class="block text-xs text-yellow-600 dark:text-yellow-400">1 day ago</span>
                            </li>
                        </ul> --}}
                    @endif
                </x-ui.card-content>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>