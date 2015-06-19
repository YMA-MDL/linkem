<?php namespace EndyJasmi\Cypher;

use EndyJasmi\Cypher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    protected $defer = false;

    public function boot()
    {
        $this->package('endyjasmi/cypher');

        $this->app->singleton('cypher', function ($app) {
            $host = $app['config']->get('cypher::host');

            return new Cypher($host);
        });
    }

    public function register()
    {
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Cypher', 'EndyJasmi\Cypher\Facades\Cypher');
        });
    }

    public function provides()
    {
        return array('cypher');
    }
}
