<?php

namespace Modules\DynamicForm\Services;

use Illuminate\Support\Str;
use Modules\Core\Services\BaseService;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldOptionRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldRepositoryInterface;

class FormFieldService extends BaseService
{
    public function __construct(
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
            'formFieldOptions',
        ]);

        return $this->formFieldRepository->fetchAll(
            filterable: $filterable,
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
        $formField = $this->formFieldRepository->store($data);

        $optionData = [];
        if (isset($formField['options']) && !blank($formField['options'])) {
            foreach ($formField['options'] as $options) {
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

        if (!blank($optionData)) {
            $this->formFieldOptionRepository->insert($optionData);
        }
    }

    /**
     * Update the Form
     *
     * @param array $data
     * @param string $formFieldId
     *
     * @return void
     */
    public function update(array $data, string $formFieldId): void
    {
        $formField = $this->formFieldRepository->update($data, $formFieldId);

        $formField->formFieldOptions()->delete();
        $optionData = [];
        if (isset($formField['options']) && !blank($formField['options'])) {
            foreach ($formField['options'] as $options) {
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

        if (!blank($optionData)) {
            $this->formFieldOptionRepository->insert($optionData);
        }
    }

    /**
     * Delete the form
     *
     * @param string $formFieldId
     *
     * @return void
     */
    public function destroy(string $formFieldId): void
    {
        $this->formFieldRepository->delete($formFieldId);
    }

}