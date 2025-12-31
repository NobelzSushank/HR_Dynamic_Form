<?php

namespace Modules\DynamicForm\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\DynamicForm\Models\FormFieldOption;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldOptionRepositoryInterface;

class FormFieldOptionRepository extends BaseRepository implements FormFieldOptionRepositoryInterface
{
    public function __construct(FormFieldOption $formFieldOption)
    {
        $this->model = $formFieldOption;
        parent::__construct();
    }
}