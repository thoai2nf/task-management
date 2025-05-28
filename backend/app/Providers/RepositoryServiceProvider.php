<?php
// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
