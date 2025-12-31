<?php

namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\User\Models\Role;
use Modules\User\Repositories\Interfaces\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $role)
    {
        $this->model = $role;
        parent::__construct();
    }

    /**
     * Query database with id and update.
     *
     * @param  array $data
     * @param  string|int $id
     *
     * @return object
     */
    public function updateRole(array $data, string|int $id): object
    {
        $updated = $this->model->where('uuid',$id)->firstOrFail();
        $updated->update($data);

        $this->cacheManager->flushAllCache();

        return $updated;
    }
}