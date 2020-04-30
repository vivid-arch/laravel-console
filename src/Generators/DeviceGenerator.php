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

namespace Vivid\Console\Generators;

use Exception;
use Vivid\Console\Components\Device;
use Vivid\Console\Str;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class DeviceGenerator extends Generator
{
    /**
     * The directories to be created under the devices directory.
     *
     * @var array
     */
    protected $directories = [
        'Console/',
        'database/',
        'database/factories/',
        'database/migrations/',
        'database/seeds/',
        'Http/',
        'Http/Controllers/',
        'Http/Middleware/',
        'Http/Requests/',
        'Providers/',
        'Features/',
        'routes',
        'Tests/',
        'Tests/Features/',
    ];

    protected $resourceDirectories = [
        'js/',
        'lang/',
        'sass/',
        'views/',
    ];

    /**
     * @param $name
     * @param bool $hasAssets
     *
     * @throws Exception
     *
     * @return bool|Device
     */
    public function generate($name, $hasAssets = true)
    {
        $name = Str::device($name);
        $slug = Str::snake($name);
        $path = $this->findDevicePath($name);

        if ($this->exists($path)) {
            throw new Exception('Device already exists!');

            return false;
        }

        $this->createDirectory($path);

        $this->createFile($path.'/.gitkeep');

        $this->createDeviceDirectories($path);

        $this->addDeviceProviders($name, $slug, $path);

        $this->addRoutesFiles($name, $slug, $path);

        if ($hasAssets) {
            $this->createResourceDirectories($name);

            $this->addWelcomeViewFile($name);
        }

        return new Device(
            $name,
            $path,
            $this->relativeFromReal($path)
        );
    }

    /**
     * Create the default directories at the given device path.
     *
     * @param string $path
     *
     * @return void
     */
    public function createDeviceDirectories($path)
    {
        foreach ($this->directories as $directory) {
            $this->createDirectory($path.'/'.$directory);
            $this->createFile($path.'/'.$directory.'/.gitkeep');
        }
    }

    /**
     * Add the corresponding device provider for the created device.
     *
     * @param string $name
     * @param string $slug
     * @param string $path
     *
     * @throws Exception
     *
     * @return void
     */
    public function addDeviceProviders(string $name, string $slug, string $path)
    {
        $namespace = $this->findDeviceNamespace($name).'\\Providers';

        $this->createRegistrationServiceProvider($name, $path, $slug, $namespace);

        $this->createRouteServiceProvider($name, $path, $slug, $namespace);
    }

    /**
     * Create the service provider that registers this device.
     *
     * @param string $name
     * @param string $path
     */
    public function createRegistrationServiceProvider($name, $path, $slug, $namespace)
    {
        $content = file_get_contents(__DIR__.'/stubs/serviceprovider.stub');
        $content = str_replace(
            ['{{name}}', '{{slug}}', '{{namespace}}'],
            [$name, $slug, $namespace],
            $content
        );

        $this->createFile($path.'/Providers/'.$name.'ServiceProvider.php', $content);
    }

    /**
     * Create the routes service provider file.
     *
     * @param string $name
     * @param string $path
     * @param string $slug
     * @param string $namespace
     *
     * @throws Exception
     */
    public function createRouteServiceProvider(string $name, string $path, string $slug, string $namespace)
    {
        $deviceNamespace = $this->findDeviceNamespace($name);
        $controllers = $deviceNamespace.'\Http\Controllers';
        $foundation = $this->findFoundationNamespace();

        $content = file_get_contents(__DIR__.'/stubs/routeserviceprovider.stub');
        $content = str_replace(
            ['{{name}}', '{{namespace}}', '{{controllers_namespace}}', '{{foundation_namespace}}'],
            [$name, $namespace, $controllers, $foundation],
            $content
        );

        $this->createFile($path.'/Providers/RouteServiceProvider.php', $content);
    }

    /**
     * Add the routes files.
     *
     * @param string $name
     * @param string $slug
     * @param string $path
     */
    public function addRoutesFiles($name, $slug, $path)
    {
        $controllers = 'app/Devices/'.$name.'/Http/Controllers';

        $contentApi = file_get_contents(__DIR__.'/stubs/routes-api.stub');
        $contentApi = str_replace(['{{slug}}', '{{controllers_path}}'], [$slug, $controllers], $contentApi);

        $contentWeb = file_get_contents(__DIR__.'/stubs/routes-web.stub');
        $contentWeb = str_replace(['{{slug}}', '{{controllers_path}}'], [$slug, $controllers], $contentWeb);

        $this->createFile($path.'/routes/api.php', $contentApi);
        $this->createFile($path.'/routes/web.php', $contentWeb);

        unset($contentApi, $contentWeb);
    }

    /**
     * Add the welcome view file.
     *
     * @param string $name
     */
    public function addWelcomeViewFile($name)
    {
        $path = resource_path('devices/'.$name);

        $this->createFile(
            $path.'/views/welcome.blade.php',
            file_get_contents(__DIR__.'/stubs/welcome.blade.stub')
        );
    }

    public function createResourceDirectories($name)
    {
        $path = resource_path('devices/'.lcfirst($name));
        foreach ($this->resourceDirectories as $directory) {
            $this->createDirectory($path.'/'.$directory);
            $this->createFile($path.'/'.$directory.'/.gitkeep');
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/service.stub';
    }

    /**
     * Add the ModelFactory file.
     *
     * @param string $path
     */
    public function addModelFactory($path)
    {
        $modelFactory = file_get_contents(__DIR__.'/stubs/model-factory.stub');
        $this->createFile($path.'/database/factories/ModelFactory.php', $modelFactory);

        unset($modelFactory);
    }
}
