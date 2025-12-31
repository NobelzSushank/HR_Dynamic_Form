<?php

namespace Modules\DynamicForm\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Services\BaseService;
use Modules\DynamicForm\Enums\FormStatusEnum;
use Modules\DynamicForm\Enums\FormSubmissionStatusEnum;
use Modules\DynamicForm\Repositories\Interfaces\FormRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormSubmissionAnswerRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormSubmissionRepositoryInterface;

class FormSubmissionService extends BaseService
{
    public function __construct(
        protected FormRepositoryInterface $formRepository,
        protected FormSubmissionRepositoryInterface $formSubmissionRepository,
        protected FormSubmissionAnswerRepositoryInterface $formSubmissionAnswerRepository,
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
            'user',
            'form',
            'formSubmissionAnswers',
            'formSubmissionAnswers.formField',
        ]);

        return $this->formSubmissionRepository->fetchAll(
            filterable: $filterable,
            with: $relationships
        );
    }

    /**
     * Show the single form
     *
     * @param string $formSubmissionId
     * @param array $relationships
     *
     * @return mixed
     */
    public function show(string $formSubmissionId, array $relationships = []): mixed
    {
        $relationships = array_merge($relationships, [
            'user',
            'form',
            'form.formFields',
            'form.formFields.formFieldOptions',
            'formSubmissionAnswers',
            'formSubmissionAnswers.formField',
        ]);

        return $this->formSubmissionRepository->fetch(
            id: $formSubmissionId,
            with: $relationships
        );

        $answers = [];
        foreach ($submission->formSubmissionAnswers as $ans) {
            $field = $ans->field;
            $answers[$field->name] = match ($field->type) {
                'number' => $ans->value_number,
                'date' => $ans->value_date,
                'checkbox' => $ans->value_json,
                default => $ans->value_text,
            };
        }
        
        return [
            'id' => $submission->id,
            'form' => [
                'id' => $submission->form->id,
                'title' => $submission->form->title,
            ],
            'user' => $submission->user,
            'status' => $submission->status,
            'submitted_at' => $submission->submitted_at,
            'answers' => $answers,
        ];
    }

    /**
     * Store the FormSubmission
     *
     * @param array $data
     * @return void
     */
    public function store(array $data): void
    {
        $form = $this->formRepository->fetch(
            id: $data['form_id'],
            with: ['formFields', 'formFields.formFieldOptions']
        );

        if ($form->status !== FormStatusEnum::PUBLISHED) {
            throw new \Exception(
                "Form is not published.",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $submission = $this->formSubmissionRepository->store([
            'form_id' => $form->id,
            'user_id' => Auth::guard('api')->id(),
            'status' => FormSubmissionStatusEnum::SUBMITTED->value,
            'submitted_at' => now(),
        ]);

        foreach ($form->formFields->keyBy('name') as $name => $field) {
            if (!array_key_exists($name, $data) && $field->type !== 'file') {
                continue;
            }
            
            $value = $data[$name] ?? null;

            $answerData = [
                'submission_id' => $submission->id,
                'form_field_id' => $field->id,
                'value_text' => null,
                'value_number' => null,
                'value_date' => null,
                'value_json' => null,
            ];

            switch ($field->type->value) {
                case 'radio':
                    $answerData['value_text'] = is_array($value) ? json_encode($value) : (string)$value;
                    break;
                case 'number':
                    $answerData['value_number'] = is_numeric($value) ? $value : null;
                    break;
                case 'date':
                    $answerData['value_date'] = $value ? \Carbon\Carbon::parse($value)->toDateString() : null;
                    break;
                case 'checkbox':
                    $answerData['value_json'] = is_array($value) ? $value : [$value];
                    break;
                default:
                    $answerData['value_text'] = is_array($value) ? json_encode($value) : (string)$value;
                    break;
            }

            $this->formSubmissionAnswerRepository->store($answerData);
        }
    }

    /**
     * Update the form
     *
     * @param array $data
     * @param string $formSubmissionId
     *
     * @return void
     */
    public function update(array $data, string $formSubmissionId): void
    {
        $submission = $this->formSubmissionRepository->fetch(
            id: $formSubmissionId,
            with: ['form', 'form.formFields', 'formSubmissionAnswers']
        );

        $form = $submission->form;

        if ($form->status === FormStatusEnum::ARCHIVED) {
            throw new \Exception(
                "Form is archived.",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        foreach ($form->formFields->keyBy('name') as $name => $field) {
            if (!array_key_exists($name, $data) && $field->type !== 'file') {
                continue;
            }
            
            $value = $data[$name] ?? null;

            $answer = $submission->formSubmissionAnswers()->where('form_field_id', $field->id)->first();
            if (!$answer) {
                $answerData = [
                    'submission_id' => $submission->id,
                    'form_field_id' => $field->id,
                    'value_text' => null,
                    'value_number' => null,
                    'value_date' => null,
                    'value_json' => null,
                ];
                
            } else {
                $answerData = [
                    'value_text' => null,
                    'value_number' => null,
                    'value_date' => null,
                    'value_json' => null,
                ];
            }

            switch ($field->type->value) {
                case 'radio':
                    $answerData['value_text'] = is_array($value) ? json_encode($value) : (string)$value;
                    break;
                case 'number':
                    $answerData['value_number'] = is_numeric($value) ? $value : null;
                    break;
                case 'date':
                    $answerData['value_date'] = $value ? \Carbon\Carbon::parse($value)->toDateString() : null;
                    break;
                case 'checkbox':
                    $answerData['value_json'] = is_array($value) ? $value : [$value];
                    break;
                default:
                    $answerData['value_text'] = is_array($value) ? json_encode($value) : (string)$value;
                    break;
            }

            $this->formSubmissionRepository->update(
                data: [
                    "status" => FormSubmissionStatusEnum::REVISED->value,
                    "submitted_at" => now()
                ],
                id: $submission->id
            );

            $this->formSubmissionAnswerRepository->update($answerData, $answer->id);
        }
    }
}