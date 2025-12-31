<?php

namespace Modules\DynamicForm\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\DynamicForm\Enums\FormSubmissionStatusEnum;
use Modules\User\Models\User;

class FormSubmission extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'form_id',
        'user_id',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => FormSubmissionStatusEnum::class,
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function formSubmissionAnswers(): HasMany
    {
        return $this->hasMany(FormSubmissionAnswer::class, 'submission_id');
    }

}
