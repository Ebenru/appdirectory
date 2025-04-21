{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    {{-- Display current profile photo --}}
    <div class="mt-6">
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Profile Photo</span>
        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="h-20 w-20 rounded-full object-cover">
    </div>

    {{-- This form sends a PATCH request to route('profile.update') --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- Add enctype for file uploads --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Bio --}}
        <div class="col-span-6 sm:col-span-4">
            <x-input-label for="bio" value="{{ __('Bio') }}" />
             <x-ui.textarea id="bio" name="bio" rows="3" class="mt-1 block w-full">{{ old('bio', $user->bio) }}</x-ui.textarea>
             <x-input-error :messages="$errors->get('bio')" class="mt-2" />
             <p class="mt-1 text-xs text-muted-foreground">Tell us a little about yourself or your connection to the campaign.</p>
        </div>

        {{-- Profile Photo Upload --}}
        <div class="col-span-6 sm:col-span-4">
            <x-input-label for="photo" value="{{ __('New Profile Photo') }}" />
            <x-text-input id="photo" name="photo" type="file" class="mt-1 block w-full border p-2 rounded-md text-sm file:mr-4 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
            <p class="mt-1 text-xs text-muted-foreground">Upload a new photo (JPG, PNG, GIF, max 2MB).</p>
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>