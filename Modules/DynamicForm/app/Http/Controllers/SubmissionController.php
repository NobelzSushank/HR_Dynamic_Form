<?php

namespace Modules\DynamicForm\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Http\Controllers\BaseController;
use Modules\DynamicForm\Http\Requests\FormSubmissionRequest;
use Modules\DynamicForm\Models\Form;
use Modules\DynamicForm\Services\FormSubmissionService;
use Modules\DynamicForm\Transformers\FormSubmissionResource;

class SubmissionController extends BaseController
{
    public function __construct(
        protected FormSubmissionService $formSubmissionService,
        protected FormSubmissionResource $formSubmissionResource
    ) {
        return parent::__construct();
    }

    /**
     * @param Request $request
     * @param string $formId
     * @return JsonResponse
     */
    public function index(Request $request, string $formId): JsonResponse
    {
        try {
            $filterable = $request->query();
            $form = $this->formSubmissionService->index($filterable);
        } catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-all-success"),
            payload: $this->formSubmissionResource->collection($form)
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            Gate::authorize('view', $id);
            $form = $this->formSubmissionService->show($id);
        } catch (AuthorizationException $e) {
            return $this->handleException($e);
        }catch (\Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            message: $this->lang("fetch-success"),
            // payload: $form
            payload: $this->formSubmissionResource->make($form)
        );
    }

    /**
     * @param FormSubmissionRequest $submissionRequest
     * @param Form $form
     * @return JsonResponse
     */
    public function store(FormSubmissionRequest $submissionRequest, Form $form)
    {
        DB::beginTransaction();

        try {
            $data = $submissionRequest->validated();
            $this->formSubmissionService->store($data);
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
     * @param FormSubmissionRequest $submissionRequest
     * @param Form $form
     * @param string $id
     * @return JsonResponse
     */
    public function update(FormSubmissionRequest $submissionRequest, Form $form, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            Gate::authorize('update', $id);
            $data = $submissionRequest->validated();
            $this->formSubmissionService->update($data, $id);
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
