<?php

namespace Tests\Unit\Services;

use App\Repositories\MachinesRepository;
use App\Services\MachinesService;
use Mockery;
use PHPUnit\Framework\TestCase;

class MachinesUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_machines_calls_default_method_when_search_is_empty(): void
    {
        $repository = Mockery::mock(MachinesRepository::class);
        $expected = (object) ['page' => 1];

        $repository->shouldReceive('getAllMachines')
            ->once()
            ->with(4, 10)
            ->andReturn($expected);
        $repository->shouldNotReceive('getSearchedMachines');

        $service = new MachinesService($repository);

        $result = $service->getAllMachines(4, 10, '');

        $this->assertSame($expected, $result);
    }

    public function test_create_machine_passes_payload_and_company_id_to_repository(): void
    {
        $repository = Mockery::mock(MachinesRepository::class);
        $payload = ['machine' => 'machine1', 'type' => 'type1'];

        $repository->shouldReceive('create')
            ->once()
            ->with($payload, 4)
            ->andReturn(true);

        $service = new MachinesService($repository);

        $result = $service->createMachine($payload, 4);

        $this->assertTrue($result);
    }

    public function test_get_machine_by_id_returns_repository_result(): void
    {
        $repository = Mockery::mock(MachinesRepository::class);
        $expected = (object) ['mid' => 14, 'machine' => 'M14'];

        $repository->shouldReceive('findMachine')
            ->once()
            ->with(14, 4)
            ->andReturn($expected);

        $service = new MachinesService($repository);

        $this->assertSame($expected, $service->getMachineById(14, 4));
    }

    public function test_delete_machine_calls_repository_delete_when_machine_exists(): void
    {
        $repository = Mockery::mock(MachinesRepository::class);

        $repository->shouldReceive('findMachine')
            ->once()
            ->with(12, 4)
            ->andReturn((object) ['mid' => 12]);
        $repository->shouldReceive('delete')
            ->once()
            ->with(12, 4)
            ->andReturn(true);

        $service = new MachinesService($repository);

        $this->assertTrue($service->deleteMachine(12, 4));
    }
}
