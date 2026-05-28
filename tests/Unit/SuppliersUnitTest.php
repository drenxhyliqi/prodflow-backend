<?php

namespace Tests\Unit\Services;

use App\Repositories\SuppliersRepository;
use App\Services\SuppliersService;
use Mockery;
use PHPUnit\Framework\TestCase;

class SuppliersUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

        public function test_get_all_suppliers_with_search_query(): void
    {
        $repository = Mockery::mock(SuppliersRepository::class);
        $expected = [(object) ['sid' => 2, 'supplier' => 'Medika']];

        $repository->shouldReceive('getSearchedSuppliers')
            ->once()
            ->with(3, 20, 'Med')
            ->andReturn($expected);

        $service = new SuppliersService($repository);
        $result = $service->getAllSuppliers(3, 20, 'Med');

        $this->assertSame($expected, $result);
    }

    public function test_get_supplier_by_id(): void
    {
        $repository = Mockery::mock(SuppliersRepository::class);
        $expected = (object) ['sid' => 5, 'supplier' => 'Aaaa'];

        $repository->shouldReceive('findSupplier')
            ->once()
            ->with(5, 3)
            ->andReturn($expected);

        $service = new SuppliersService($repository);
        $result = $service->getSupplierById(5, 3);

        $this->assertSame($expected, $result);
    }

    public function test_find_or_fail_returns_bool(): void
    {
        $repository = Mockery::mock(SuppliersRepository::class);

        $repository->shouldReceive('checkSupplierExist')
            ->once()
            ->with(12, 3)
            ->andReturn(true);

        $service = new SuppliersService($repository);
        $this->assertTrue($service->findOrFail(12, 3));
    }

    public function test_create_supplier_calls_repository_create(): void
    {
        $repository = Mockery::mock(SuppliersRepository::class);
        $payload = ['supplier' => 'New Supplier'];

        $repository->shouldReceive('create')
            ->once()
            ->with($payload, 3)
            ->andReturn(true);

        $service = new SuppliersService($repository);
        $this->assertTrue($service->createSupplier($payload, 3));
    }

    public function test_update_supplier_calls_repository_when_supplier_exists(): void
    {
        $repository = Mockery::mock(SuppliersRepository::class);
        $payload = ['supplier' => 'Updated Supplier Name'];

        $repository->shouldReceive('findSupplier')
            ->once()
            ->with(10, 3)
            ->andReturn((object) ['sid' => 10]);

        $repository->shouldReceive('update')
            ->once()
            ->with(10, $payload, 3)
            ->andReturn(true);

        $service = new SuppliersService($repository);
        $this->assertTrue($service->updateSupplier(10, $payload, 3));
    }

    public function test_delete_supplier_calls_repository_when_supplier_exists(): void
    {
        $repository = Mockery::mock(SuppliersRepository::class);

        $repository->shouldReceive('findSupplier')
            ->once()
            ->with(15, 3)
            ->andReturn((object) ['sid' => 15]);

        $repository->shouldReceive('delete')
            ->once()
            ->with(15, 3)
            ->andReturn(true);

        $service = new SuppliersService($repository);
        $this->assertTrue($service->deleteSupplier(15, 3));
    }

    public function test_delete_supplier_returns_false_when_supplier_does_not_exist(): void
    {
        $repository = Mockery::mock(SuppliersRepository::class);

        $repository->shouldReceive('findSupplier')->once()->with(15, 3)->andReturn(null);
        $repository->shouldNotReceive('delete');

        $service = new SuppliersService($repository);
        $this->assertFalse($service->deleteSupplier(15, 3));
    }
}