<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Company::class, 'company');
    }

    public function index()
    {
        return CompanyResource::collection(Company::with('user')->paginate());
    }

    public function show(Company $company)
    {
        return new CompanyResource($company);
    }

    public function myCompany(Request $request)
    {
        $user = $request->user();

        if (!$request->user()->employer) {
            return response()->json([
                'message' => 'Company profile does not exist for this user'
            ], 404);
        }

        return new CompanyResource($user->employer);
    }

    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->user()->employer) {
            return response()->json([
                'message' => 'Company profile already exists for this user'
            ], 409);
        }

        return new CompanyResource(Company::create($data));
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return new CompanyResource($company);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        
        return response()->json([
            'message' => 'Company deleted successfully'
        ]);
    }
}
