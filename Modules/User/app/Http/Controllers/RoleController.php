<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Http\Requests\RoleRequest;
use Modules\User\Services\RoleService;
use Modules\User\Transformers\RoleResource;

class RoleController extends BaseController
{
    public function __construct(
        protected RoleService $roleService,
        protected RoleResource $roleResource,
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
            $roles = $this->roleService->index($filterable);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-all-success"),
            payload: $this->roleResource->collection($roles)
        );
    }

    /**
     * Store
     *
     * @param RoleRequest $roleRequest
     *
     * @return JsonResponse
     */
    public function store(RoleRequest $roleRequest): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $roleRequest->validated();
            $this->roleService->store($data);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }

        DB::commit();
        return $this->successResponse(
            message: $this->lang("create-success"),
            responseCode: Response::HTTP_CREATED
        );
    }

    /**
     * Update
     *
     * @param RoleRequest $roleRequest
     * @param string $id
     *
     * @return JsonResponse
     */
    public function update(RoleRequest $roleRequest, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $roleRequest->validated();
            $this->roleService->update($data, $id);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }

        DB::commit();
        return $this->successResponse(
            message: $this->lang("update-success")
        );
    }

    /**
     * Destroy
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $role = $this->roleService->destroy($id);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }

        DB::commit();
        return $this->successResponse(
            message: $this->lang("delete-success")
        );
    }
}
