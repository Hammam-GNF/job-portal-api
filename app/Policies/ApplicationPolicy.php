<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Application $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEmployer()) {
            return $application->jobListing->company_id === $user->employer->company->id;
        }

        return $application->user_id === $user->id;
    }

    public function review(User $user, Application $application): bool
    {
        return $user->isEmployer() && $user->employer->company->id === $application->jobListing->company_id;
    }

    public function decide(User $user, Application $application): bool
    {
        return $user->isEmployer() && $user->employer->company->id === $application->jobListing->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isApplicant();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Application $application): bool
    {
        return $user->isAdmin();
    }

    public function suspend(User $user, Application $application): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    
    public function restore(User $user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Application $application): bool
    {
        return false;
    }
}
