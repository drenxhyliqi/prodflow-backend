<?php

namespace App\Http\Controllers;

use App\Services\ClientsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class Clients extends Controller
{
    protected ClientsService $service;
    public function __construct(ClientsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            if ($this->service->createClient($request->only(['client', 'phone', 'location']))) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client registered successfully.'
                ], 201);
            } else {
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
        return $this->service->getAllClients(10);
    }
    //---------------
    public function edit($id)
    {
        if (!$this->service->findOrFail($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found.'
            ], 404);
        } else {
            return $this->service->getClientById($id);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:clients,cid',
            'client' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'All fields must be filled in according to the rules.');
        } else {
            return $this->service->updateClient($request->input('id'), $request->only(['client', 'phone', 'location']));
        }
    }
    //---------------
    public function delete($id)
    {
        return $this->service->deleteClient($id);
    }
}
