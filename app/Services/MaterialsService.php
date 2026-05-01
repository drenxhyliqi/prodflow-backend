<?php

namespace App\Services;

use App\Repositories\MaterialsRepository;

class MaterialsService
{
    protected MaterialsRepository $repository;
    public function __construct(MaterialsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllMaterials(int $limit, int $companyId, string $search = '')
    {
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            return $this->repository->getAllMaterials($limit, $companyId, $search);
        } else {
            return $this->repository->getAllMaterials($limit, $companyId, $search);
        }
    }
    //---------------
    public function getMaterialById(int $id, int $companyId)
    {
        return $this->repository->findMaterialById($id, $companyId);
    }
    //---------------
    public function checkMaterialExist(int $id, int $companyId): bool
    {
        return $this->repository->checkMaterialExist($id, $companyId);
    }
    //---------------
    public function createMaterial(array $data, int $companyId): bool
    {
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function hasDuplicateMaterial(
        string $material,
        string $unit,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return $this->repository->hasDuplicateMaterial($material, $unit, $companyId, $excludeId);
    }
    //---------------
    public function updateMaterial(int $id, array $data, int $companyId): bool
    {
        $material = $this->repository->findMaterialById($id, $companyId);

        if (! $material) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteMaterial(int $id, int $companyId): bool
    {
        $material = $this->repository->findMaterialById($id, $companyId);

        if (! $material) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
}
