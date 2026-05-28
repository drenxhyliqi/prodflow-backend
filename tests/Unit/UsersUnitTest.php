<?php

namespace Tests\Unit\Services;

use App\Models\UsersModel;
use App\Repositories\UsersRepository;
use App\Services\UsersService;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\TestCase;

class UsersUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_users_with_default_limit_and_no_search(): void
    {
        $repository = Mockery::mock(UsersRepository::class);
        $expected = [(object) ['uid' => 1, 'user' => 'hello']];

        $repository->shouldReceive('getUsers')
            ->once()
            ->with(10)
            ->andReturn($expected);

        $service = new UsersService($repository);
        $result = $service->getUsers(0, '');

        $this->assertSame($expected, $result);
    }

    public function test_get_users_with_search_query(): void
    {
        $repository = Mockery::mock(UsersRepository::class);
        $expected = [(object) ['uid' => 2, 'user' => 'Donart']];

        $repository->shouldReceive('getSearchedUsers')
            ->once()
            ->with('Don', 15)
            ->andReturn($expected);

        $service = new UsersService($repository);
        $result = $service->getUsers(15, 'Don');

        $this->assertSame($expected, $result);
    }

    public function test_create_user_hashes_password_properly(): void
    {
        $repository = Mockery::mock(UsersRepository::class);
        $payload = ['username' => 'hello', 'password' => 'secret123'];

        Hash::shouldReceive('make')
            ->once()
            ->with('secret123')
            ->andReturn('hashed_secret123');

        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($argument) {
                return $argument['password'] === 'hashed_secret123';
            }))
            ->andReturn(true);

        $service = new UsersService($repository);
        $this->assertTrue($service->createUser($payload));
    }

    public function test_update_user_keeps_old_password_if_not_provided(): void
    {
        $repository = Mockery::mock(UsersRepository::class);
        $existingUser = (object) ['uid' => 5, 'password' => 'existing_hash'];
        $payload = ['username' => 'a_new', 'password' => ''];

        $repository->shouldReceive('findUser')->once()->with(5)->andReturn($existingUser);
        $repository->shouldReceive('update')
            ->once()
            ->with(5, Mockery::on(function ($argument) {
                return $argument['password'] === 'existing_hash';
            }))
            ->andReturn(true);

        $service = new UsersService($repository);
        $this->assertTrue($service->updateUser(5, $payload));
    }

    public function test_check_user_returns_false_on_wrong_password(): void
    {
        $repository = Mockery::mock(UsersRepository::class);
        $userMock = (object) ['username' => 'user', 'password' => 'hashed_pass'];

        $repository->shouldReceive('findByUsername')->once()->with('user')->andReturn($userMock);
        Hash::shouldReceive('check')->once()->with('gabim', 'hashed_pass')->andReturn(false);

        $service = new UsersService($repository);
        $result = $service->checkUser(['username' => 'user', 'password' => 'gabim']);

        $this->assertFalse($result);
    }
}