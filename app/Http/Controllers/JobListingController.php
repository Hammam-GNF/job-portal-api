<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobListingRequest;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Http\Request;

class JobListingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(JobListing::class, 'jobListing');
    }

    // public function index(Request $request)
    // {
    //     $query = JobListing::with(['company', 'category']);
        
    //     if ($request->user()?->isEmployer()) {
    //         $query->where('company_id', $request->user()->employer->company->id);
    //     }

    //     return JobListingResource::collection($query->paginate());
    // }

    public function index()
    {
        $jobListings = JobListing::with(['company', 'category'])->where('status', 'open')->paginate();

        return JobListingResource::collection($jobListings);
    }

    public function show(JobListing $jobListing)
    {
        return new JobListingResource($jobListing);
    }
    
    public function store(JobListingRequest $request)
    {
        $data = $request->validated();
        
        $data['company_id'] = $request->user()->employer->id;
        $data['status'] = 'open';

        $jobListing = JobListing::create($data)->load(['company', 'category']);
        
        return new JobListingResource($jobListing);
    }
    
    public function update(JobListingRequest $request, JobListing $jobListing)
    {
        $jobListing->update($request->validated());

        return new JobListingResource($jobListing);
    }


    public function destroy(JobListing $jobListing) 
    {
        $jobListing->delete();
        
        return response()->json([
            'message' => 'Job deleted successfully'
        ]);
    }
}
