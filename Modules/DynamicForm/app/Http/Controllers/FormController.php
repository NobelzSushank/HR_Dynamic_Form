<?php

namespace Modules\DynamicForm\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\BaseController;
use Modules\DynamicForm\Http\Requests\FormRequest;
use Modules\DynamicForm\Services\FormService;
use Modules\DynamicForm\Transformers\FormResource;

class FormController extends BaseController
{
    public function __construct(
        protected FormService $formService,
        protected FormResource $formResource
    ) {
        return parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filterable = $request->query();
            $form = $this->formService->index($filterable);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-all-success"),
            payload: $this->formResource->collection($form)
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $form = $this->formService->show($id);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-success"),
            payload: $this->formResource->make($form)
        );
    }

    /**
     * @param FormRequest $formRequest
     * @return JsonResponse
     */
    public function store(FormRequest $formRequest): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $formRequest->validated();
            $this->formService->store($data);
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
     * @param FormRequest $formRequest
     * @param string $id
     * @return JsonResponse
     */
    public function update(FormRequest $formRequest, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $formRequest->validated();
            $this->formService->update($data, $id);
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
     * Delete Form
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $deletedData = $this->formService->destroy($id);
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->handleException($exception);
        }

        DB::commit();

        return $this->successResponse(
            message: $this->lang("delete-success")
        );
    }

    /**
     * @param FormRequest $formRequest
     * @param string $id
     * @return JsonResponse
     */
    public function publish(FormRequest $formRequest, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $formRequest->validated();
            $this->formService->publish($data, $id);
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->handleException($exception);
        }

        DB::commit();

        return $this->successResponse(
            message: $this->lang("update-success")
        );
    }

}
