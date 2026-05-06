<?php

namespace App\Http\Controllers;

use App\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Users extends Controller
{
    protected UsersService $service;
    public function __construct(UsersService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required|string|min:1|max:255',
            'username' => 'required|string|min:1|max:255',
            'password' => 'required|string|min:1|max:255',
            'company_id' => 'required|numeric|min:1|exists:companies,cid',
            'role' => 'required|string|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            if($this->service->getUserByUsername($request->username)){
                return response()->json([
                    'success' => false,
                    'message' => 'This username already exist. Choose another one!'
                ], 500);
            }

            if($this->service->createUser($request->only(['user', 'username', 'password', 'company_id', 'role']))){
                return response()->json([
                    'success' => true,
                    'message' => 'User registered successfully.'
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
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        return response()->json(
            $this->service->getUsers(10, $search)
        );
    }
    //---------------
    public function edit(int $id)
    {
        if(!$this->service->findOrFail($id)){
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }else{
            return $this->service->getUserById($id);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|numeric|min:1|exists:users,uid',
            'user' => 'required|string|min:1|max:255',
            'username' => 'required|string|min:1|max:255',
            'password' => 'nullable|string|min:1|max:255',
            'company_id' => 'required|numeric|min:1|exists:companies,cid',
            'role' => 'required|string|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $user = $this->service->getUserByUsername($request->username);
            if($user){
                if ((int)$user->uid !== (int)$request->uid){
                    return response()->json([
                        'success' => false,
                        'message' => 'This username already exist. Choose another one!'
                    ], 422);
                }
            }

            if ($this->service->updateUser($request->input('uid'), $request->only(['user', 'username', 'password', 'company_id', 'role']))) {
                return response()->json([
                    'success' => true,
                    'message' => 'The user was successfully updated.'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No data was updated.'
                ], 500);
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        if ($this->service->deleteUser($id)) {
            return response()->json([
                'success' => true,
                'message' => 'The user was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
    //---------------
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $user = $this->service->checkUser($request->only(['username', 'password']));
            if($user){
                return response()->json($user, 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong Credentials.'
                ], 401);
            }
        }
    }
    //---------------
    public function updateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required|string|min:3|max:255',
            'password' => 'required|string|min:8|max:255',
            'new_password' => 'nullable|string|min:8|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $username = $request->user()->username;
            $user = $this->service->updateAccount($username, $request->only(['user', 'password', 'new_password']));
            if($user){
                $user = $request->user();
                $user->user = $request->user;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'The user was successfully updated.'
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong Credentials.'
                ], 401);
            }
        }
    }
    //---------------
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
    //---------------
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
