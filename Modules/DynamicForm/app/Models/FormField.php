<?php

namespace Modules\DynamicForm\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\DynamicForm\Enums\FormFieldsTypeEnum;

class FormField extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'form_id',
        'label',
        'name',
        'type',
        'required',
        'order',
        'validation',
        'meta',
    ];

    protected function casts()
    {
        return [
            'type' => FormFieldsTypeEnum::class,
            'required' => 'boolean',
            'order' => 'integer',
            'meta' => 'array',
            'validation' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function formFieldOptions(): HasMany
    {
        return $this->hasMany(FormFieldOption::class, 'form_field_id');
    }

    public function formSubmissionAnswers(): HasMany
    {
        return $this->hasMany(FormSubmissionAnswer::class, 'form_field_id');
    }
}
