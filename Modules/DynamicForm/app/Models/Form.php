<?php

namespace Modules\DynamicForm\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\DynamicForm\Enums\FormStatusEnum;
use Modules\User\Models\User;

// use Modules\DynamicForm\Database\Factories\FormFactory;

class Form extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
        'published_at',
    ];

    protected function casts()
    {
        return [
            'status' => FormStatusEnum::class,
            'published_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class, 'form_id');
    }

    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }
}
