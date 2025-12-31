<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Services\PermissionService;
use Modules\User\Transformers\PermissionResource;

class PermissionController extends BaseController
{
    public function __construct(
        protected PermissionService $permissionService,
        protected PermissionResource $permissionResource,
    ) {
        $this->modelName = 'User';
        parent::__construct();
    }

    /**
     * Index
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filterable = $request->query();
            $permissions = $this->permissionService->index($filterable);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-all-success"),
            payload: $this->permissionResource->collection($permissions)
        );
    }
}
