<?php

namespace Tests\Unit\Services;

use App\Repositories\ClientsRepository;
use App\Services\ClientsService;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClientsUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_clients(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);
        $expected = [(object) ['cid' => 1], (object) ['cid' => 2]];

        $repository->shouldReceive('getAllClients')
            ->once()
            ->with(7)
            ->andReturn($expected);

        $service = new ClientsService($repository);
        $result = $service->getAllClients(7);

        $this->assertSame($expected, $result);
    }

    public function test_get_client_by_id(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);
        $expected = (object) ['cid' => 15, 'client' => 'Aaaa'];

        $repository->shouldReceive('findClient')
            ->once()
            ->with(15, 7)
            ->andReturn($expected);

        $service = new ClientsService($repository);
        $result = $service->getClientById(15, 7);

        $this->assertSame($expected, $result);
    }

    public function test_get_clients_with_search_query(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);
        $expected = [(object) ['cid' => 1, 'client' => 'Test']];

        $repository->shouldReceive('getSearchedClients')
            ->once()
            ->with(7, 15, 'Aaaa')
            ->andReturn($expected);

        $service = new ClientsService($repository);
        $result = $service->getClients(7, 15, 'Aaaa');

        $this->assertSame($expected, $result);
    }

    public function test_find_or_fail_returns_repository_check(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);

        $repository->shouldReceive('checkClientExist')
            ->once()
            ->with(15, 7)
            ->andReturn(true);

        $service = new ClientsService($repository);
        
        $this->assertTrue($service->findOrFail(15, 7));
    }

    public function test_create_client_calls_repository_create(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);
        $payload = ['client' => 'New Client'];

        $repository->shouldReceive('create')
            ->once()
            ->with($payload, 7)
            ->andReturn(true);

        $service = new ClientsService($repository);

        $this->assertTrue($service->createClient($payload, 7));
    }

    public function test_update_client_calls_repository_update_when_client_exists(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);
        $payload = ['client' => 'Updated Client', 'phone' => '111', 'location' => 'Pr'];

        $repository->shouldReceive('findClient')
            ->once()
            ->with(99, 3)
            ->andReturn((object) ['cid' => 99]);
        $repository->shouldReceive('update')
            ->once()
            ->with(99, $payload, 3)
            ->andReturn(true);

        $service = new ClientsService($repository);

        $this->assertTrue($service->updateClient(99, $payload, 3));
    }

    public function test_update_client_returns_false_when_client_does_not_exist(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);
        $payload = ['client' => 'Updated Client'];

        $repository->shouldReceive('findClient')
            ->once()
            ->with(99, 3)
            ->andReturn(null);
        $repository->shouldNotReceive('update');

        $service = new ClientsService($repository);

        $this->assertFalse($service->updateClient(99, $payload, 3));
    }

    public function test_delete_client_calls_repository_delete_when_client_exists(): void
    {
        $repository = Mockery::mock(ClientsRepository::class);

        $repository->shouldReceive('findClient')
            ->once()
            ->with(15, 3)
            ->andReturn((object) ['cid' => 15]);
        $repository->shouldReceive('delete')
            ->once()
            ->with(15, 3)
            ->andReturn(true);

        $service = new ClientsService($repository);

        $this->assertTrue($service->deleteClient(15, 3));
    }

}