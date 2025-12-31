<?php

namespace Modules\DynamicForm\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\BaseController;
use Modules\DynamicForm\Http\Requests\FormFieldRequest;
use Modules\DynamicForm\Services\FormFieldService;
use Modules\DynamicForm\Transformers\FormFieldResource;

class FormFieldController extends BaseController
{
    public function __construct(
        protected FormFieldService $formFieldService,
        protected FormFieldResource $formFieldResource
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
            $form = $this->formFieldService->index($filterable);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-all-success"),
            payload: $this->formFieldResource->collection($form)
        );
    }

    /**
     * @param FormFieldRequest $formFieldRequest
     * @param string $formId
     * @return JsonResponse
     */
    public function store(FormFieldRequest $formFieldRequest, string $formId): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $formFieldRequest->validated();
            $this->formFieldService->store($data);
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
     * @param FormFieldRequest $formFieldRequest
     * @param string $formId
     * @param string $id
     * @return JsonResponse
     */
    public function update(FormFieldRequest $formFieldRequest, string $formId, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $formFieldRequest->validated();
            $this->formFieldService->update($data, $id);
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
     * @param string $formId
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $formId, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $deletedData = $this->formFieldService->destroy($id);
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
