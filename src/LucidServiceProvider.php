<?php

/*
 * This file is part of the vivid-console project.
 *
 * Copyright for portions of project lucid-console are held by VineLab, 2016 as part of Lucid Architecture.
 * All other copyright for project Vivid Architecture are held by Meletios Flevarakis, 2019.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vivid\Console;

use Illuminate\Support\ServiceProvider;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class LucidServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $configPath = __DIR__.'/../config/lucid.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');

        $dashboardEnabled = $this->app['config']->get('lucid.dashboard');

        if ($dashboardEnabled === null) {
            $dashboardEnabled = $this->app['config']->get('app.debug');
        }

        if ($dashboardEnabled === true) {
            if (!$this->app->routesAreCached()) {
                require_once __DIR__.'/Http/routes.php';
            }
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lucid');

        $this->publishes([
             __DIR__.'/../resources/assets' => public_path('vendor/lucid'),
        ], 'public');
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $configPath = __DIR__.'/../config/lucid.php';
        $this->mergeConfigFrom($configPath, 'lucid');

        //$this->app->register(LogReaderServiceProvider::class);
    }

    /**
     * Return path to config file.
     *
     * @return string
     */
    private function getConfigPath()
    {
        return config_path('lucid.php');
    }
}
