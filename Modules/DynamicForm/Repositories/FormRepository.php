<?php

namespace Modules\DynamicForm\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\DynamicForm\Models\Form;
use Modules\DynamicForm\Repositories\Interfaces\FormRepositoryInterface;

class FormRepository extends BaseRepository implements FormRepositoryInterface
{
    public function __construct(Form $form)
    {
        $this->model = $form;
        parent::__construct();
    }
}