<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
    public function isSignupAvailable(): bool
    {
        return $this->repository->countAll() === 0;
    }
    //---------------
    public function registerFirstUser(array $data)
    {
        if (!$this->isSignupAvailable()) {
            return false;
        }

        return DB::transaction(function () use ($data) {
            if ($this->repository->countAll() > 0) {
                return false;
            }

            $companyId = DB::table('companies')->insertGetId([
                'name' => $data['name'],
                'sector' => $data['sector'],
                'location' => $data['location'],
                'status' => 'Active',
            ]);

            $created = $this->repository->create([
                'user' => $data['user'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'company_id' => $companyId,
                'role' => 'admin',
            ]);

            if (!$created) {
                return false;
            }

            $user = $this->repository->findByUsername($data['username']);
            if (!$user) {
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
                    'role' => $user->role,
                ],
            ];
        });
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
    //---------------
    public function createInvitation(array $data): array
    {
        if ($this->repository->findByUsername($data['username'])) {
            return [
                'success' => false,
                'message' => 'This username is already in use.',
            ];
        }

        $token = Str::random(64);
        $payload = [
            'user' => $data['user'],
            'username' => $data['username'],
            'company_id' => $data['company_id'],
            'role' => $data['role'],
            'token' => $token,
            'status' => 'pending',
            'expires_at' => now()->addHours(48),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (!$this->repository->createInvitation($payload)) {
            return [
                'success' => false,
                'message' => 'Failed to create invitation. Please try again.',
            ];
        }

        $frontendBaseUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $inviteUrl = $frontendBaseUrl . '/accept-invite?token=' . $token;

        return [
            'success' => true,
            'message' => 'Invitation created successfully.',
            'token' => $token,
            'invite_link' => $inviteUrl,
            'expires_at' => $payload['expires_at'],
        ];
    }
    //---------------
    public function getInvitations(int $limit = 10, string $search = '')
    {
        return $this->repository->getInvitations($limit, $search);
    }
    //---------------
    public function getValidInvitationByToken(string $token)
    {
        return $this->repository->findValidInvitationByToken($token);
    }
    //---------------
    public function acceptInvitation(string $token, string $password): array
    {
        $invitation = $this->repository->findValidInvitationByToken($token);
        if (!$invitation) {
            return [
                'success' => false,
                'message' => 'Invitation is invalid or expired.',
            ];
        }

        if ($this->repository->findByUsername($invitation->username)) {
            $this->repository->updateInvitationStatus((int) $invitation->iid, 'accepted');
            return [
                'success' => false,
                'message' => 'This account already exists.',
            ];
        }

        $created = DB::transaction(function () use ($invitation, $password) {
            $userCreated = $this->repository->create([
                'user' => $invitation->user,
                'username' => $invitation->username,
                'password' => Hash::make($password),
                'company_id' => $invitation->company_id,
                'role' => $invitation->role,
            ]);

            if (!$userCreated) {
                return false;
            }

            return $this->repository->updateInvitationStatus((int) $invitation->iid, 'accepted');
        });

        if (!$created) {
            return [
                'success' => false,
                'message' => 'Failed to accept invitation. Please try again.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Invitation accepted successfully. You can now log in.',
        ];
    }
    //---------------
    public function revokeInvitation(int $id): bool
    {
        return $this->repository->updateInvitationStatus($id, 'revoked');
    }
}
