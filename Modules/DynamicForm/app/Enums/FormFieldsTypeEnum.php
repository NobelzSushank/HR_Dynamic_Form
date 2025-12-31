<?php

namespace Modules\DynamicForm\Enums;

enum FormFieldsTypeEnum: string
{
    case TEXT = "text";
    case NUMBER = "number";
    case DATE = "date";
    case SELECT = "select";
    case RADIO = "radio";
    case CHECKBOX = "checkbox";
    case FILE = "file";
    case TEXTAREA = "textarea";
    
    public static function getAllValues(): array
    {
        return array_column(self::cases(), "value");
    }
}
