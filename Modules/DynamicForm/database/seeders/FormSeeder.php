<?php

namespace Modules\DynamicForm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DynamicForm\Enums\FormStatusEnum;
use Modules\DynamicForm\Models\Form;
use Modules\DynamicForm\Models\FormField;
use Modules\DynamicForm\Models\FormFieldOption;
use Modules\User\Models\User;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where(['email' => 'hr@company.com'])->first();

        $form = Form::firstOrCreate(
            ['title' => 'Employee Onboarding'],
            [
                'description' => 'Basic onboarding form',
                'status' => FormStatusEnum::PUBLISHED->value,
                'created_by' => $user->id,
                'published_at' => now(),
            ]
        );
        
        // Full Name field
        $fullName = FormField::firstOrCreate(
            [
                'form_id' => $form->id,
                'label' => 'Full Name',
                'name' => 'full_name',
            ],
            [
                'type' => 'text',
                'required' => true,
                'order' => 1,
                'validation' => json_encode(['min' => 3, 'max' => 100]),
                'meta' => json_encode(['placeholder' => 'Enter your full legal name']),
            ]
        );
        
        // Gender field
        $gender = FormField::firstOrCreate(
            [
                'form_id' => $form->id,
                'label' => 'Gender',
                'name' => 'gender',
                'type' => 'radio',
            ],
            [
                'required' => true,
                'order' => 2,
            ]
        );
        
        $options = [ 
            ['value' => 'male', 'label' => 'Male'],
            ['value' => 'female', 'label' => 'Female'],
            ['value' => 'other', 'label' => 'Other'],
        ];
        
        foreach ($options as $i => $opt) {
            FormFieldOption::firstOrCreate(
                [
                    'form_field_id' => $gender->id,
                    'value' => $opt['value'],
                    'label' => $opt['label'],
                ],
                ['order' => $i + 1,]
            );
        }
    }
}
