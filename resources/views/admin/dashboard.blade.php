<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    {{-- Top Row Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Pending Submissions Card --}}
        <x-ui.card>
            <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                <x-ui.card-title class="text-sm font-medium">Pending Submissions</x-ui.card-title>
                <x-lucide-loader class="h-4 w-4 text-muted-foreground" />
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="text-2xl font-bold text-primary">{{ $pendingPeopleCount + $pendingCompanyCount }}</div>
                <p class="text-xs text-muted-foreground">
                    {{ $pendingPeopleCount }} People / {{ $pendingCompanyCount }} Orgs
                </p>
            </x-ui.card-content>
            <x-ui.card-footer>
                <x-ui.button variant="outline" size="sm" as-child>
                    <a href="{{ route('admin.pending') }}">Review Pending</a>
                </x-ui.button>
            </x-ui.card-footer>
        </x-ui.card>

        {{-- Approved People Card --}}
        <x-ui.card>
            <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                <x-ui.card-title class="text-sm font-medium">Approved People</x-ui.card-title>
                <x-lucide-user-check class="h-4 w-4 text-muted-foreground" />
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="text-2xl font-bold">{{ $approvedPeopleCount }}</div>
                <p class="text-xs text-muted-foreground">Total listed individuals</p>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Approved Companies Card --}}
        <x-ui.card>
            <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                <x-ui.card-title class="text-sm font-medium">Approved Organizations</x-ui.card-title>
                <x-lucide-building-2 class="h-4 w-4 text-muted-foreground" />
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="text-2xl font-bold">{{ $approvedCompanyCount }}</div>
                <p class="text-xs text-muted-foreground">Total listed organizations</p>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Total Users Card --}}
        <x-ui.card>
            <x-ui.card-header class="flex flex-row items-center justify-between space-y-0 pb-2">
                <x-ui.card-title class="text-sm font-medium">Total Users</x-ui.card-title>
                <x-lucide-users class="h-4 w-4 text-muted-foreground" />
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="text-2xl font-bold">{{ $totalUsers }}</div>
                <p class="text-xs text-muted-foreground">
                    +{{ $newUsersLast30d }} in last 30 days
                </p>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    {{-- Second Row: Chart and Recent Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Submissions Chart --}}
        <x-ui.card class="lg:col-span-2">
            <x-ui.card-header>
                <x-ui.card-title>Submissions (Last {{ count($submissionsChartData['labels']) }} Days)</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content>
                <canvas id="submissionsChart"></canvas> {{-- Canvas for Chart.js --}}
            </x-ui.card-content>
        </x-ui.card>

        {{-- Recent Users --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Recent Users</x-ui.card-title>
                <x-ui.card-description>Last 5 registered users.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content class="space-y-4">
                @forelse($recentUsers as $user)
                <div class="flex items-center space-x-3">
                    {{-- <img class="h-8 w-8 rounded-full" src="..." alt=""> --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                            {{ $user->name }}
                        </p>
                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                            {{ $user->email }}
                        </p>
                    </div>
                    <div class="inline-flex items-center text-xs text-muted-foreground">
                        {{ $user->created_at->diffForHumans(null, true) }} ago {{-- Short diff --}}
                    </div>
                </div>
                @empty
                <p class="text-sm text-muted-foreground">No recent user registrations.</p>
                @endforelse
            </x-ui.card-content>
        </x-ui.card>
    </div>

    {{-- Third Row: Recent Pending Lists --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Recent Pending People --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Recent Pending People</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recentPendingPeople as $person)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $person->fullName }}</p>
                            <p class="text-xs text-muted-foreground">Submitted by: {{ $person->submittedBy->name ?? 'Unknown' }}</p>
                        </div>
                        <x-ui.button variant="ghost" size="sm" as-child>
                            <a href="{{ route('admin.pending') }}">Review</a>
                        </x-ui.button>
                    </li>
                    @empty
                    <p class="text-sm text-muted-foreground">No recent pending people.</p>
                    @endforelse
                </ul>
            </x-ui.card-content>
        </x-ui.card>

        {{-- Recent Pending Companies --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Recent Pending Organizations</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recentPendingCompanies as $company)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->displayName ?? $company->legalName }}</p>
                            <p class="text-xs text-muted-foreground">Submitted by: {{ $company->submittedBy->name ?? 'Unknown' }}</p>
                        </div>
                        <x-ui.button variant="ghost" size="sm" as-child>
                            <a href="{{ route('admin.pending') }}">Review</a>
                        </x-ui.button>
                    </li>
                    @empty
                    <p class="text-sm text-muted-foreground">No recent pending organizations.</p>
                    @endforelse
                </ul>
            </x-ui.card-content>
        </x-ui.card>

    </div>

    {{-- Add Chart.js Initialization Script --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('submissionsChart');
            if (ctx) {
                // Retrieve data passed from controller (ensure it's JSON encoded)
                const chartData = @json($submissionsChartData);

                new Chart(ctx, {
                    type: 'line', // or 'bar'
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Adjust as needed
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    // Ensure only whole numbers are shown on y-axis
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                        // Add other Chart.js options here
                    }
                });
            }
        });
    </script>
    @endpush

</x-app-layout>