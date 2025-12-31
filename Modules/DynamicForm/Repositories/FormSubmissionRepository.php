<?php

namespace Modules\DynamicForm\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\DynamicForm\Models\FormSubmission;
use Modules\DynamicForm\Repositories\Interfaces\FormSubmissionRepositoryInterface;

class FormSubmissionRepository extends BaseRepository implements FormSubmissionRepositoryInterface
{
    public function __construct(FormSubmission $formSubmission)
    {
        $this->model = $formSubmission;
        parent::__construct();
    }
}