<?php

namespace App\Http\Controllers;

use App\Models\CompaniesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Companies extends Controller
{
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
                'message' => 'Të gjitha fushat duhet të plotësohen sipas rregullave.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            if(CompaniesModel::createCompany($request->name, $request->sector, $request->location)){
                return response()->json([
                    'success' => true,
                    'message' => 'Kompania u regjistrua me sukses.'
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Ndodhi një gabim gjatë ruajtjes së të dhënave. Ju lutemi provoni përsëri.'
                ], 500);
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $search = $request->query('search');
        $perPage = (int) ($request->query('per_page') ?? 10);
        $companies = CompaniesModel::getCompanies($page, $search, $perPage);
        return response()->json([
            'success' => true,
            'message' => 'Lista e kompanive u shfaq me sukses.',
            'data'    => $companies['data'] ?? $companies,
            'meta'    => [
                'current_page' => $companies['current_page'] ?? $page,
                'per_page'     => $companies['per_page'] ?? $perPage,
                'total'        => $companies['total'] ?? null,
                'last_page'    => $companies['last_page'] ?? null,
            ]
        ], 200);
    }
}
