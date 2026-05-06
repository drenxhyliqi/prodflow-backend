<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    protected UsersRepository $repository;
    public function __construct(UsersRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getUsers(int $limit = 10, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedUsers($search, $limit);
        }
        return $this->repository->getUsers($limit);
    }
    //---------------
    public function getUserById(int $id)
    {
        return $this->repository->findUser($id);
    }
    //---------------
    public function getUserByUsername(string $username)
    {
        return $this->repository->findByUsername($username);
    }
    //---------------
    public function findOrFail(int $id)
    {
        return $this->repository->checkUserExist($id);
    }
    //---------------
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->repository->create($data);
    }
    //---------------
    public function updateUser(int $id, array $data): bool
    {
        $user = $this->repository->findUser($id);
        if (!$user) {
            return false;
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = $user->password;
        }
        return $this->repository->update($id, $data);
    }
    //---------------
    public function deleteUser(int $id): bool
    {
        $user = $this->repository->findUser($id);
        if (!$user) {
            return false;
        }
        return $this->repository->delete($id);
    }
    //---------------
    public function checkUser(array $data)
    {
        $user = $this->repository->findByUsername($data['username']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return false;
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'token' => $token,
            'user' => [
                'uid' => $user->uid,
                'user' => $user->user,
                'username' => $user->username,
                'company_id' => $user->company_id,
                'role' => $user->role
            ]
        ];
    }
    //---------------
    public function updateAccount(string $username, array $data)
    {
        $user = $this->repository->findByUsername($username);
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return false;
        }
        $updateData = [
            'user' => $data['user'],
        ];
        if (!empty($data['new_password'])) {
            $updateData['password'] = Hash::make($data['new_password']);
        }
        return $this->repository->updateAccount($username, $updateData);
    }
}
