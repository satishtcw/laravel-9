<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Helpers\ElasticsearchHelper;
use App\Utilities\Contracts\RedisHelperInterface;
use App\Utilities\Helpers\RedisHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ElasticsearchHelperInterface::class, ElasticsearchHelper::class);
        $this->app->bind(RedisHelperInterface::class, RedisHelper::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
