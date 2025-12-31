<?php

namespace Modules\DynamicForm\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\DynamicForm\Models\FormField;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldRepositoryInterface;

class FormFieldRepository extends BaseRepository implements FormFieldRepositoryInterface
{
    public function __construct(FormField $formField)
    {
        $this->model = $formField;
        parent::__construct();
    }
}