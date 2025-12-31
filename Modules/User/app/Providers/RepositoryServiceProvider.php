<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\Repositories\Interfaces\PermissionRepositoryInterface;
use Modules\User\Repositories\Interfaces\RoleRepositoryInterface;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;
use Modules\User\Repositories\PermissionRepository;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            abstract: UserRepositoryInterface::class,
            concrete: UserRepository::class
        );

        $this->app->bind(
            abstract: RoleRepositoryInterface::class,
            concrete: RoleRepository::class
        );

        $this->app->bind(
            abstract: PermissionRepositoryInterface::class,
            concrete: PermissionRepository::class
        );
    }
}
