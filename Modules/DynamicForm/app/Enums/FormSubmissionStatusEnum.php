<?php

namespace Modules\DynamicForm\Enums;

enum FormSubmissionStatusEnum: string
{
    case SUBMITTED = "submitted";
    case REVISED = "revised";
    
    public static function getAllValues(): array
    {
        return array_column(self::cases(), "value");
    }
}
