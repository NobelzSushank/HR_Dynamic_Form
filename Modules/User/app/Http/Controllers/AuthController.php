<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\BaseController;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Services\Auth\AuthService;

class AuthController extends BaseController
{
    public function __construct(
        protected AuthService $authService,
    ) {
        return parent::__construct();
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request = $request->validated();
            $data = $this->authService->login($request);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        return $this->successResponse(
            payload: $data
        );
    }

    /**
     * refresh
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $this->authService->refresh();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception);
        }
        DB::commit();

        return $this->successResponse(
            payload: $data
        );
    }

    /**
     * Logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang(
                key: "auth.logout"
            ),
        );
    }
}
