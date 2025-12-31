<?php

namespace Modules\User\Services;

use Modules\Core\Services\BaseService;
use Modules\User\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionService extends BaseService
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
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
        return $this->permissionRepository->fetchAll(
            filterable: $filterable,
            with: $relationships
        );
    }
}