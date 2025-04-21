<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate; // Keep if using Gates
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Person; // Import models
use App\Policies\PersonPolicy; // Import policies
use App\Models\Company;
use App\Policies\CompanyPolicy;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Register policies for models
        Person::class => PersonPolicy::class, // <-- ADD
        Company::class => CompanyPolicy::class, // <-- ADD
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies(); // This line should already be here

        // Define Gates here if needed (alternative to policies)
    }
}
