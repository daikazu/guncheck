<?php

namespace Daikazu\Guncheck;


use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class GuncheckServiceProvider extends ServiceProvider
{
    protected $defer = false;


    public function boot()
    {

        $app = $this->app;
        $this->bootConfig();

        $app['validator']->extend('guncheck', function ($attribute, $value, $parameters, $validator) {

            $req = (new Client())->get('https://api.mailgun.net/v3/address/validate', [
                'query' => [
                    'address' => $value,
                    'api_key' => config('guncheck.api_key'),
                ],
                'auth'  => null,
            ]);

            return json_decode($req->getBody()->getContents())->isValid;
        }, 'This email is invalid');


    }


    protected function bootConfig()
    {
        $path = __DIR__ . '/config/guncheck.php';
        $this->mergeConfigFrom($path, 'guncheck');
        if (function_exists('config_path')) {
            $this->publishes([$path => config_path('guncheck.php')]);
        }
    }


}