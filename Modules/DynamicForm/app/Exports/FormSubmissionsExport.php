<?php

namespace Modules\DynamicForm\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\DynamicForm\Models\Form;

class FormSubmissionsExport implements FromCollection, WithHeadings
{
    public function __construct(private Form $form)
    {}


    public function headings(): array
    {
        $fieldNames = $this->form->formFields()->orderBy('order')->pluck('name')->all();
        return array_merge(['Submission ID','Employee Email','Submitted At'], $fieldNames);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $subs = $this->form->formSubmissions()->with(['user', 'formSubmissionAnswers', 'formSubmissionAnswers.formField'])->get();
        return $subs->map(function($s){
            $row = [$s->id, $s->user->email, optional($s->submitted_at)->toDateTimeString()];
            $fields = $s->formSubmissionAnswers->sortBy(fn($a) => $a->formField->order);
            foreach ($fields as $a) {
                $row[] = $this->formatValue($a);
            }
            return collect($row);
        });

    }

    protected function formatValue($a) {
        if (!is_null($a->value_text)) return $a->value_text;
        if (!is_null($a->value_number)) return $a->value_number;
        if (!is_null($a->value_date)) return $a->value_date;
        if (!is_null($a->value_json)) return json_encode($a->value_json);
        return null;
    }

}
