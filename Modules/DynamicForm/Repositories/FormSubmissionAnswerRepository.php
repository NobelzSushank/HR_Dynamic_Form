<?php

namespace Modules\DynamicForm\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\DynamicForm\Models\FormSubmissionAnswer;
use Modules\DynamicForm\Repositories\Interfaces\FormSubmissionAnswerRepositoryInterface;

class FormSubmissionAnswerRepository extends BaseRepository implements FormSubmissionAnswerRepositoryInterface
{
    public function __construct(FormSubmissionAnswer $formSubmissionAnswer)
    {
        $this->model = $formSubmissionAnswer;
        parent::__construct();
    }
}