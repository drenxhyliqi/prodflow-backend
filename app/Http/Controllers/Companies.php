<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class Companies extends Controller
{
    protected CompanyService $service;
    public function __construct(CompanyService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:255',
            'sector' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            if($this->service->createCompany($request->only(['name', 'sector', 'location']))){
                return response()->json([
                    'success' => true,
                    'message' => 'Company registered successfully.'
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving the data. Please try again.'
                ], 500);
            }
        }
    }
    //---------------
    public function read()
    {
        return $this->service->getAllCompanies(10);
    }
    //---------------
    public function edit($id)
    {
        if(!$this->service->findOrFail($id)){
            return response()->json([
                'success' => false,
                'message' => 'Company not found.'
            ], 404);
        }else{
            return $this->service->getCompanyById($id);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:companies,id',
            'name' => 'required|string|min:1|max:255',
            'sector' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'All fields must be filled in according to the rules.');
        } else {
            return $this->service->updateCompany($request->input('id'), $request->only(['name', 'sector', 'location']));
        }
    }
    //---------------
    public function delete($id)
    {
        return $this->service->deleteCompany($id);
    }
}
