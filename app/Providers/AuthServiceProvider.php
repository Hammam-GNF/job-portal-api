<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;
use App\Policies\ApplicationPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\JobListingPolicy;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Category::class => CategoryPolicy::class,
        JobListing::class => JobListingPolicy::class,
        Application::class => ApplicationPolicy::class,
    ];

    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
