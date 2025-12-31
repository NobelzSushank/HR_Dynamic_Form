<?php

namespace Modules\DynamicForm\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\DynamicForm\Repositories\FormFieldOptionRepository;
use Modules\DynamicForm\Repositories\FormFieldRepository;
use Modules\DynamicForm\Repositories\FormRepository;
use Modules\DynamicForm\Repositories\FormSubmissionAnswerRepository;
use Modules\DynamicForm\Repositories\FormSubmissionRepository;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldOptionRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormFieldRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormSubmissionAnswerRepositoryInterface;
use Modules\DynamicForm\Repositories\Interfaces\FormSubmissionRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void {
        $this->app->bind(
            abstract: FormRepositoryInterface::class,
            concrete: FormRepository::class
        );

        $this->app->bind(
            abstract: FormFieldRepositoryInterface::class,
            concrete: FormFieldRepository::class
        );

        $this->app->bind(
            abstract: FormFieldOptionRepositoryInterface::class,
            concrete: FormFieldOptionRepository::class
        );

        $this->app->bind(
            abstract: FormSubmissionRepositoryInterface::class,
            concrete: FormSubmissionRepository::class
        );

        $this->app->bind(
            abstract: FormSubmissionAnswerRepositoryInterface::class,
            concrete: FormSubmissionAnswerRepository::class
        );
    }
}
