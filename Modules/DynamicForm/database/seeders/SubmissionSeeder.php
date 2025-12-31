<?php

namespace Modules\DynamicForm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DynamicForm\Models\Form;
use Modules\DynamicForm\Models\FormField;
use Modules\DynamicForm\Models\FormSubmission;
use Modules\DynamicForm\Models\FormSubmissionAnswer;
use Modules\User\Models\User;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = User::where('email', 'employee@company.com')->first();

        $form = Form::first();
        
        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'user_id' => $employee->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $fullNameField = FormField::where('name', 'full_name')->first();
        $genderField = FormField::where('name', 'gender')->first();

        FormSubmissionAnswer::create([
            'submission_id' => $submission->id,
            'form_field_id' => $fullNameField->id,
            'value_text' => 'John Employee',
        ]);
        
        FormSubmissionAnswer::create([
            'submission_id' => $submission->id,
            'form_field_id' => $genderField->id,
            'value_text' => 'male',
        ]);
    }
}
