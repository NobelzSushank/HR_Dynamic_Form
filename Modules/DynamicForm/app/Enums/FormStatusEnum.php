<?php

namespace Modules\DynamicForm\Enums;

enum FormStatusEnum: string
{
    case DRAFT = "draft";
    case PUBLISHED = "published";
    case ARCHIVED = "archived";

    public static function getAllValues(): array
    {
        return array_column(self::cases(), "value");
    }
}
