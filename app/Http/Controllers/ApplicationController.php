<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Application::class, 'application');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $query = Application::query();
        } else if($user->isEmployer()) {
            $query = Application::whereHas('jobListing', fn ($q) => $q->where('company_id', $user->employer->company->id));
        } else {
            $query = Application::where('user_id', $user->id);
        }

        $applications = $query->paginate();

        if (! $applications = $query->exists()) {
            return response()->json([
                'message' => 'No applications found'
            ], 404);
        }

        return ApplicationResource::collection($query->with('jobListing')->get());
    }
    
    public function show(Application $application)
    {
        return new ApplicationResource($application);
    }

    public function store(ApplicationRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'submitted';

        if(Application::where('user_id', $data['user_id'])
            ->where('job_listing_id', $data['job_listing_id'])->exists())
        {
            return response()->json([
                'message' => 'You have already applied for this job'
            ], 422);
        }

        $application = Application::create($data)->load('user', 'jobListing');

        return new ApplicationResource($application);
    }

    public function indexByJob(JobListing $jobListing)
    {
        if (!$jobListing->applications()->exists()) {
            return response()->json([
                'message' => 'No applications found for this job'
            ], 404);
        }
        
        $this->authorize('viewApplications', $jobListing);
        
        return ApplicationResource::collection($jobListing->applications()->with('user')->get());
    }

    public function review(ApplicationRequest $request, Application $application)
    {
        $this->authorize('review', $application);
        
        if ($application->status !== 'submitted') {
            return response()->json([
                'message' => 'Only submitted applications can be reviewed'
            ], 400);
        }

        $application->update([
            'status' => 'reviewed',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Application reviewed',
            'data' => new ApplicationResource($application)
        ]);
    }

    public function accept(Application $application)
    {
        if ($application->status !== 'reviewed') {
            abort(422, 'Application must be reviewed first');
        }

        $application->update([
            'status' => 'accepted',
        ]);

        return response()->json([
            'message' => 'Application accepted',
            'data' => new ApplicationResource($application)
        ]);
    }

    public function reject(Application $application)
    {
        if ($application->status !== 'reviewed') {
            abort(422, 'Application must be reviewed first');
        }

        $application->update([
            'status' => 'rejected',
        ]);

        return response()->json([
            'message' => 'Application rejected',
            'data' => new ApplicationResource($application)
        ]);
    }

    public function update()
    {
        abort(403, 'Applications cannot be updated');
    }

    public function destroy(Application $application)
    {
        $application->delete();
        
        return response()->json([
            'message' => 'Application deleted successfully'
        ]);
    }
}
