<?php

namespace Modules\DynamicForm\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFieldOption extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'form_field_id',
        'value',
        'label',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer'
        ];
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }
}
