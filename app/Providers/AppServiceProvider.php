<?php

namespace App\Providers;

use App\Services\CurrencyConvertor;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('currency.convertor', function () {
            return new CurrencyConvertor(config('services.currency_convertor.api_key'));
        });

        if (App::environment('production')) {
            // path.public this name laravel already known and it's stored in services container public_path().
            $this->app->singleton('path.public', function () {
                // public_html this name according of name of file in server.
                return base_path('public_html');
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {



        // this's will remove the data key from response json.
        // this's will applied on single product.
        JsonResource::withoutWrapping();

        Validator::extend('filter', function ($attribute, $value, $params) {
            return !in_array(strtolower($value), $params);
        }, 'This Name Is Forbidden');


        Paginator::useBootstrapFour();
    }
}
