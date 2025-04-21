{{-- This is a Blade component for a category card that displays an icon, name, and count of contributors.
     It uses Tailwind CSS for styling and assumes you have a Blade icon package installed for rendering icons. --}}

{{-- The component accepts the following props:
    - name: The name of the category
    - count: The number of contributors in that category
    - icon: The icon name (e.g., 'briefcase', 'users')
    - slug: The slug for the category (used in the route)
    - type: The type of category ('people' or 'companies')}}
{{-- resources/views/components/category-card.blade.php --}}
@props([
    'name',
    'count',
    'icon', // Expecting a Lucide icon name (e.g., 'briefcase', 'users')
    'slug',
    'type' // 'people' or 'companies'
])

@php
    // Determine the correct route based on the type
    $route = $type === 'people' ? route('people.index', ['category' => $slug]) : route('companies.index', ['category' => $slug]);

    // Prepare the dynamic component name for a Blade icon package
    // Assumes you have a package like 'blade-ui-kit/blade-lucide-icons' or 'mallardduck/blade-lucide-icons' installed
    // The package provides components like <x-lucide-briefcase />, <x-lucide-users />, etc.
    $iconComponent = 'lucide-' . strtolower($icon);
@endphp

{{-- The main link wrapping the card --}}
<a href="{{ $route }}" {{ $attributes->merge(['class' => 'group']) }}> {{-- Moved group here --}}

  {{-- Recreate the Card structure using divs and Tailwind classes from app.css --}}
  <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden transition-all hover:shadow-md hover:bg-accent">

    {{-- Recreate the Card Content structure --}}
    <div class="p-4 flex flex-col items-center justify-center text-center">

      {{-- Icon Area --}}
      <div class="mb-2 text-primary group-hover:scale-110 transition-transform">
        {{--
            Render the Blade icon component dynamically.
            IMPORTANT: Make sure you have a Blade icon package installed (like blade-ui-kit/blade-lucide-icons)
            that provides components matching the $iconComponent name (e.g., <x-lucide-briefcase />).
            If not, you'll need to implement icon rendering differently (e.g., using SVG includes).
        --}}
        <x-dynamic-component :component="$iconComponent" class="h-6 w-6" />
      </div>

      {{-- Text Content --}}
      <h3 class="font-medium text-sm text-foreground">{{ $name }}</h3> {{-- Use text-foreground for main text --}}
      <p class="text-xs text-muted-foreground">{{ $count }} contributor{{ $count !== 1 ? 's' : '' }}</p>

    </div> {{-- End Card Content div --}}

  </div> {{-- End Card div --}}
</a>