<?php

namespace Modules\DynamicForm\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmissionAnswer extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'submission_id',
        'form_field_id',
        'value_text',
        'value_number',
        'value_date',
        'value_json',
    ];

    protected function casts(): array
    {
        return [
            'value_number' => 'decimal',
            'value_json' => 'array',
            'value_date' => 'date',
        ];
    }

    public function formSubmission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'submission_id');
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }
}
