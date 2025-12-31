<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Http\Requests\UserPasswordRequest;
use Modules\User\Http\Requests\UserRequest;
use Modules\User\Services\UserService;
use Modules\User\Transformers\UserResource;

class UserController extends BaseController
{
    public function __construct(
        protected UserService $userService,
        protected UserResource $userResource,
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
            $users = $this->userService->index($filterable);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-all-success"),
            payload: $this->userResource->collection($users)
        );
    }

    /**
     * Show
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->userService->show($id);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-success"),
            payload: $this->userResource->make($user)
        );
    }

    /**
     * Store
     *
     * @param UserRequest $userRequest
     *
     * @return JsonResponse
     */
    public function store(UserRequest $userRequest): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $userRequest->validated();
            $this->userService->store($data);
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
     * @param UserRequest $userRequest
     * @param string $id
     *
     * @return JsonResponse
     */
    public function update(UserRequest $userRequest, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $userRequest->validated();
            $this->userService->update($data, $id);
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
     * Update Password
     *
     * @param UserPasswordRequest $userPasswordRequest
     * @param string $id
     *
     * @return JsonResponse
     */
    public function updatePassword(UserPasswordRequest $userPasswordRequest): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $userPasswordRequest->validated();
            $this->userService->updatePassword($data);
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
            $user = $this->userService->destroy($id);
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
