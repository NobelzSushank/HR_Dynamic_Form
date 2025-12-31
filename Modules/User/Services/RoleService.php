<?php

namespace Modules\User\Services;

use Modules\Core\Services\BaseService;
use Modules\User\Repositories\Interfaces\RoleRepositoryInterface;

class RoleService extends BaseService
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {
    }

    /**
     * Index
     *
     * @param array $filterable
     * @param array $relationships
     *
     * @return mixed
     */
    public function index(array $filterable, array $relationships = []): mixed
    {

        $relationships = array_merge($relationships, [
            "permissions",
        ]);

        return $this->roleRepository->fetchAll(
            filterable: $filterable,
            with: $relationships
        );
    }

    /**
     * Store
     *
     * @param array $data
     *
     * @return void
     */
    public function store(array $data): void
    {
        $role = $this->roleRepository->store($data);
        $role->givePermissionTo($data['permissions']);

    }

    /**
     * Update
     *
     * @param array $data
     * @param string $id
     *
     * @return void
     */
    public function update(array $data, string $id): void
    {
        $role = $this->roleRepository->updateRole($data, $id);
        $role->permissions()->detach();
        $role->givePermissionTo($data['permissions']);
    }

    /**
     * Soft Delete
     *
     * @param string $id
     *
     * @return object
     */
    public function destroy(string $id): object
    {
        return $this->roleRepository->delete($id);
    }
}