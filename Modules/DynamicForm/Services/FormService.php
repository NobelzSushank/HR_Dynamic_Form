<?php

namespace Modules\DynamicForm\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Core\Services\BaseService;
use Modules\DynamicForm\Enums\FormStatusEnum;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldOptionRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormRepositoryInterface;

class FormService extends BaseService
{
    public function __construct(
        protected FormRepositoryInterface $formRepository,
        protected FormFieldRepositoryInterface $formFieldRepository,
        protected FormFieldOptionRepositoryInterface $formFieldOptionRepository
    ) {
    }

    /**
     * Fetch all the form
     *
     * @param array $filterable
     * @param array $relationships
     *
     * @return mixed
     */
    public function index(array $filterable, array $relationships = []): mixed
    {
        $relationships = array_merge($relationships, [
            'createdBy',
            'formFields',
            'formFields.formFieldOptions',
            'formSubmissions',
            'formSubmissions.formSubmissionAnswers',
        ]);

        return $this->formRepository->fetchAll(
            filterable: $filterable,
            with: $relationships
        );
    }

    /**
     * Show the single form
     *
     * @param string $formId
     * @param array $relationships
     *
     * @return mixed
     */
    public function show(string $formId, array $relationships = []): mixed
    {
        $relationships = array_merge($relationships, [
            'createdBy',
            'formFields',
            'formFields.formFieldOptions',
            'formSubmissions',
            'formSubmissions.formSubmissionAnswers',
        ]);

        return $this->formRepository->fetch(
            id: $formId,
            with: $relationships
        );
    }

    /**
     * Store the Form
     *
     * @param array $data
     * @return void
     */
    public function store(array $data): void
    {
        $data['created_by'] = Auth::user()->id;
        $data['published_at'] = now();

        $form = $this->formRepository->store($data);

        foreach ($data['formFields'] as $fieldValue) {
            
            $fieldValue['form_id'] = $form->id;

            $formField = $this->formFieldRepository->store($fieldValue);

            $optionData = [];
            if (isset($fieldValue['options']) && !blank($fieldValue['options'])) {
                foreach ($fieldValue['options'] as $options) {
                    $optionData[] = [
                        'id' => Str::uuid(),
                        'form_field_id' => $formField->id,
                        'value' => $options['value'],
                        'label' => $options['label'],
                        'order' => $options['order'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        if (!blank($optionData)) {
            $this->formFieldOptionRepository->insert($optionData);
        }

    }

    /**
     * Update the form
     *
     * @param array $data
     * @param string $formId
     *
     * @return void
     */
    public function update(array $data, string $formId): void
    {
        $this->formRepository->update(
            data: $data,
            id: $formId
        );
    }

    /**
     * Delete the form
     *
     * @param string $formId
     *
     * @return void
     */
    public function destroy(string $formId): void
    {
        $this->formRepository->delete($formId);
    }

    /**
     * Publish the form
     *
     * @param array $data
     * @param string $formId
     *
     * @return void
     */
    public function publish(array $data, string $formId): void
    {
        abort_unless(
            in_array(
                FormStatusEnum::getAllValues(),
                [
                    FormStatusEnum::DRAFT->value,
                    FormStatusEnum::ARCHIVED->value
                ],
            ),
            Response::HTTP_FORBIDDEN
        );

        $this->update(
            data: $data,
            formId: $formId
        );
    }

}