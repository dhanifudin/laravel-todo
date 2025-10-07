<?php

namespace App\Providers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Testing\TestResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TestResponse::macro('withViewErrors', function (ViewErrorBag $errors) {
            if (! isset($this->original) || ! $this->original instanceof View) {
                throw new \BadMethodCallException('The response is not a view.');
            }

            $this->original->with(['errors' => $errors]);

            return $this;
        });
    }
}
