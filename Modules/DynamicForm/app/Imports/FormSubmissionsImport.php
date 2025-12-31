<?php

namespace Modules\DynamicForm\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\DynamicForm\Enums\FormSubmissionStatusEnum;
use Modules\DynamicForm\Models\Form;
use Modules\DynamicForm\Models\FormSubmission;
use Modules\DynamicForm\Models\FormSubmissionAnswer;
use Modules\User\Models\User;

class FormSubmissionsImport implements ToCollection
{
    public function __construct(
        protected Form $form,
    ) {}

    public function collection(Collection $rows)
    {
        $head = $rows->shift();
        $map = $this->mapHeadingsToFields($head);

        foreach($rows as $row) {
            $email = $row[0];
            
            $user = User::where('email', $email)->firstOrFail();
            
            $submission = FormSubmission::create([
                'form_id' => $this->form->id,
                'user_id' => $user->id,
                'submitted_at' => now(),
                'status' => FormSubmissionStatusEnum::SUBMITTED->value,
            ]);
            
            foreach ($map as $index => $field) {
                $value = $row[$index] ?? null;
                $this->storeAnswer($submission, $field, $value);
            }
        }
    }

    protected function mapHeadingsToFields($head): array {
        $fields = $this->form->formFields()->get()->keyBy('name');
        $map = [];
        foreach ($head as $i => $col) {
            if ($field = $fields->get($col)) $map[$i] = $field;
        }
        return $map;
    }

    protected function storeAnswer($submission, $field, $value): void {
        $data = ['submission_id' => $submission->id, 'form_field_id' => $field->id];
        switch ($field->type) {
            case 'number': $data['value_number'] = is_numeric($value) ? $value : null; break;
            case 'date': $data['value_date'] = $value ? \Carbon\Carbon::parse($value)->toDateString() : null; break;
            case 'checkbox': $data['value_json'] = $value ? explode('|', $value) : []; break;
            default: $data['value_text'] = $value;
        }
        
        FormSubmissionAnswer::create($data);
    }


}