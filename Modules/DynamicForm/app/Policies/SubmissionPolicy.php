<?php

namespace Modules\DynamicForm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\DynamicForm\Models\FormSubmission;
use Modules\User\Models\User;

class SubmissionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function view(User $user, FormSubmission $submission): bool
    {
        return $user->hasRole(['Admin','HR']) || $submission->user_id === $user->id;
    }

    public function update(User $user, FormSubmission $submission): bool
    {
        return $submission->user_id === $user->id;
    }

}
