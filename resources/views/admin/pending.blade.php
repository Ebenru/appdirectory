{{-- resources/views/admin/pending.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pending Submissions
        </h2>
    </x-slot>

    <div class="space-y-8">
        {{-- Pending People --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Pending People ({{ $pendingPeople->count() }})</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content>
                @if($pendingPeople->isEmpty())
                <p class="text-muted-foreground">No pending people submissions.</p>
                @else
                <div class="overflow-x-auto"> {{-- Add scroll for small screens --}}
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contributor</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                                <th scope="col" class="relative px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pendingPeople as $person)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($person->picture_url)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $person->picture_url }}" alt="">
                                        </div>
                                        @endif
                                        <div class="{{ $person->picture_url ? 'ml-4' : '' }}">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $person->fullName }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $person->title ?? '--' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-300 max-w-xs truncate" title="{{ $person->description }}">{{ $person->description ?? '--' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Category: {{ $person->category_display_name }}</div>
                                    {{-- Add a non-public admin detail link later if needed --}}
                                    {{-- <a href="#" class="text-xs text-primary hover:underline">View Full Details (Admin)</a> --}}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $person->created_at->format('Y-m-d') }} by {{ $person->submittedBy->name ?? 'Unknown' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    {{-- Approve Form --}}
                                    <form method="POST" action="{{ route('admin.people.approve', $person) }}" class="inline-block">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" size="sm" variant="outline" class="text-green-600 border-green-600 hover:bg-green-50">
                                            <x-lucide-check class="w-4 h-4" />
                                        </x-ui.button>
                                    </form>
                                    {{-- Reject Form --}}
                                    <form method="POST" action="{{ route('admin.people.reject', $person) }}" class="inline-block">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" size="sm" variant="destructive-outline">
                                            <x-lucide-x class="w-4 h-4" />
                                        </x-ui.button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm space-x-2">
                                    {{-- Edit Button --}}
                                    @can('update', $person)
                                    <x-ui.button variant="outline" size="sm" as-child>
                                        <a href="{{ route('people.edit', $person) }}">Edit</a>
                                    </x-ui.button>
                                    @endcan

                                    <!-- {{-- Delete Button/Form --}}
                                    @can('delete', $person)
                                    <form method="POST" action="{{ route('people.destroy', $person) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this submission?');"> {{-- Add JS confirm --}}
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button variant="destructive-outline" size="sm" type="submit">
                                            Delete
                                        </x-ui.button>
                                    </form>
                                    @endcan -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </x-ui.card-content>
        </x-ui.card>

        {{-- Pending Companies --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Pending Organizations ({{ $pendingCompanies->count() }})</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content>
                @if($pendingCompanies->isEmpty())
                <p class="text-muted-foreground">No pending organization submissions.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Organization</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                                <th scope="col" class="relative px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pendingCompanies as $company)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($company->logo_url)
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 dark:bg-gray-700 rounded-md flex items-center justify-center p-1">
                                            <img class="max-h-full max-w-full object-contain" src="{{ $company->logo_url }}" alt="">
                                        </div>
                                        @endif
                                        <div class="{{ $company->logo_url ? 'ml-4' : '' }}">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->displayName ?? $company->legalName }}</div>
                                            @if($company->displayName && $company->displayName !== $company->legalName)
                                            <div class="text-xs text-gray-400">{{ $company->legalName }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-300 max-w-xs truncate" title="{{ $company->description }}">{{ $company->description ?? '--' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Category: {{ $company->category_display_name }}</div>
                                    {{-- <a href="#" class="text-xs text-primary hover:underline">View Full Details (Admin)</a> --}}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $company->created_at->format('Y-m-d') }} by {{ $company->submittedBy->name ?? 'Unknown' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    {{-- Approve Form --}}
                                    <form method="POST" action="{{ route('admin.companies.approve', $company) }}" class="inline-block">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" size="sm" variant="outline" class="text-green-600 border-green-600 hover:bg-green-50">
                                            <x-lucide-check class="w-4 h-4" />
                                        </x-ui.button>
                                    </form>
                                    {{-- Reject Form --}}
                                    <form method="POST" action="{{ route('admin.companies.reject', $company) }}" class="inline-block">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" size="sm" variant="destructive-outline">
                                            <x-lucide-x class="w-4 h-4" />
                                        </x-ui.button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm space-x-2">
                                    {{-- Edit/Delete Buttons Placeholder --}}
                                    @can('update', $company)
                                    @if($company->status === 'pending')
                                    <x-ui.button variant="outline" size="sm" as-child>
                                        <a href="{{ route('companies.edit', $company) }}">Edit</a>
                                    </x-ui.button>
                                    @endif
                                    @endcan

                                    <!-- @can('delete', $company)
                                    <form method="POST" action="{{ route('companies.destroy', $company) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this submission?');"> {{-- Add JS confirm --}}
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button variant="destructive-outline" size="sm" type="submit">
                                            Delete
                                        </x-ui.button>
                                    </form>
                                    @endcan -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </x-ui.card-content>
        </x-ui.card>
    </div>

</x-app-layout>