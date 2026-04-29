<?php

namespace App\Services;

use App\Repositories\MaterialsRepository;

class MaterialsService
{
    public function __construct(
        protected MaterialsRepository $repository
    ) {}

    public function getAllMaterials(int $limit, ?int $companyId = null)
    {
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            return $this->repository->getSearchedMaterials($_GET['search'], $limit, $companyId);
        } else {
            return $this->repository->getAllMaterials($limit, $companyId);
        }
    }

    public function getMaterialById(int $id, ?int $companyId = null)
    {
        return $this->repository->findMaterialById($id, $companyId);
    }

    public function checkMaterialExist(int $id, ?int $companyId = null): bool
    {
        return $this->repository->checkMaterialExist($id, $companyId);
    }

    public function createMaterial(array $data): bool
    {
        return $this->repository->create($data);
    }

    public function hasDuplicateMaterial(
        string $material,
        string $unit,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return $this->repository->hasDuplicateMaterial($material, $unit, $companyId, $excludeId);
    }

    public function updateMaterial(int $id, array $data, ?int $companyId = null): bool
    {
        $material = $this->repository->findMaterialById($id, $companyId);

        if (! $material) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteMaterial(int $id, ?int $companyId = null): bool
    {
        $material = $this->repository->findMaterialById($id, $companyId);

        if (! $material) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
}
