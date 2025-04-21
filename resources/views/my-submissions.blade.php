<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Submissions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- People Submissions --}}
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>People You Submitted</x-ui.card-title>
                </x-ui.card-header>
                <x-ui.card-content>
                    @if($people->isEmpty())
                    <p class="text-muted-foreground text-sm">You haven't submitted any people yet.
                        <a href="{{ route('people.create') }}" class="text-primary hover:underline">Submit one now!</a>
                    </p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase">Submitted</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-muted-foreground uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($people as $person)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('people.show', $person) }}" class="text-sm font-medium text-primary hover:underline" target="_blank" title="View public page (if approved)">
                                            {{ $person->fullName }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <x-ui.badge variant="{{ match($person->status) {'approved' => 'success', 'rejected' => 'destructive', default => 'secondary'} }}">
                                            {{ ucfirst($person->status) }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-muted-foreground">
                                        {{ $person->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm space-x-2">
                                        {{-- Edit Button --}}
                                        @can('update', $person)
                                        <x-ui.button variant="outline" size="sm" as-child>
                                            <a href="{{ route('people.edit', $person) }}">Edit</a>
                                        </x-ui.button>
                                        @endcan

                                        {{-- Delete Button/Form --}}
                                        @can('delete', $person)
                                        <form method="POST" action="{{ route('people.destroy', $person) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this submission?');"> {{-- Add JS confirm --}}
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button variant="destructive-outline" size="sm" type="submit">
                                                Delete
                                            </x-ui.button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination for People --}}
                    <div class="mt-4">
                        {{ $people->links() }}
                    </div>
                    @endif
                </x-ui.card-content>
            </x-ui.card>

            {{-- Company Submissions --}}
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>Organizations You Submitted</x-ui.card-title>
                </x-ui.card-header>
                <x-ui.card-content>
                    @if($companies->isEmpty())
                    <p class="text-muted-foreground text-sm">You haven't submitted any organizations yet.
                        <a href="{{ route('companies.create') }}" class="text-primary hover:underline">Submit one now!</a>
                    </p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-muted-foreground uppercase">Submitted</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-muted-foreground uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($companies as $company)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('companies.show', $company) }}" class="text-sm font-medium text-primary hover:underline" target="_blank" title="View public page (if approved)">
                                            {{ $company->displayName ?? $company->legalName }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <x-ui.badge variant="{{ match($company->status) {'approved' => 'success', 'rejected' => 'destructive', default => 'secondary'} }}">
                                            {{ ucfirst($company->status) }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-muted-foreground">
                                        {{ $company->created_at->format('Y-m-d') }}
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

                                        @can('delete', $company)
                                        <form method="POST" action="{{ route('companies.destroy', $company) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this submission?');"> {{-- Add JS confirm --}}
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button variant="destructive-outline" size="sm" type="submit">
                                                Delete
                                            </x-ui.button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination for Companies --}}
                    <div class="mt-4">
                        {{ $companies->links() }}
                    </div>
                    @endif
                </x-ui.card-content>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>