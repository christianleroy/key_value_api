<?php

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;

afterEach(function () {
    URL::forceScheme(null);
});

it('forces https scheme in production', function () {
    $this->app['env'] = 'production';
    (new AppServiceProvider($this->app))->boot();

    expect(URL::to('/test'))->toStartWith('https://');
});

it('does not force https scheme outside production', function () {
    foreach (['local', 'staging', 'testing'] as $env) {
        $this->app['env'] = $env;
        (new AppServiceProvider($this->app))->boot();

        expect(URL::to('/test'))->toStartWith('http://');
        URL::forceScheme(null);
    }
});
