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

class DeviceGenerator extends Generator
{
    /**
     * The directories to be created under the devices' directory.
     */
    protected array $directories = [
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

    protected array $resourceDirectories = [
        'js/',
        'lang/',
        'sass/',
        'views/',
    ];

    /**
     * Generate a new device.
     *
     * @throws Exception
     */
    public function generate(string $name, bool $noAssets): Device
    {
        $name = Str::device($name);
        $slug = Str::snake($name);
        $path = $this->findDevicePath($name);

        if ($this->exists($path)) {
            throw new Exception('Device already exists!');
        }

        $this->createDirectory($path);

        $this->createDeviceDirectories($path);

        $this->addDeviceProviders($name, $slug, $path);

        $this->addRoutesFiles($name, $slug, $path);

        if (!$noAssets) {
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
     */
    public function createDeviceDirectories(string $path): void
    {
        foreach ($this->directories as $directory) {
            $this->createDirectory($path . '/' . $directory);
        }
    }

    /**
     * Add the corresponding device provider for the created device.
     *
     * @throws Exception
     */
    public function addDeviceProviders(string $name, string $slug, string $path): void
    {
        $namespace = $this->findDeviceNamespace($name) . '\\Providers';

        $this->createRegistrationServiceProvider($name, $path, $slug, $namespace);

        $this->createRouteServiceProvider($name, $path, $slug, $namespace);
    }

    /**
     * Create the service provider that registers this device.
     */
    public function createRegistrationServiceProvider(string $name, string $path, $slug, $namespace): void
    {
        $content = file_get_contents(__DIR__ . '/stubs/serviceprovider.stub');
        $content = str_replace(
            ['{{name}}', '{{slug}}', '{{namespace}}'],
            [$name, $slug, $namespace],
            $content
        );

        $this->createFile($path . '/Providers/' . $name . 'ServiceProvider.php', $content);
    }

    /**
     * Create the routes service provider file.
     *
     * @throws Exception
     */
    public function createRouteServiceProvider(string $name, string $path, string $slug, string $namespace): void
    {
        $deviceNamespace = $this->findDeviceNamespace($name);
        $controllers = $deviceNamespace . '\Http\Controllers';
        $foundation = $this->findFoundationNamespace();

        $content = file_get_contents(__DIR__ . '/stubs/routeserviceprovider.stub');
        $content = str_replace(
            ['{{name}}', '{{namespace}}', '{{controllers_namespace}}', '{{foundation_namespace}}'],
            [$name, $namespace, $controllers, $foundation],
            $content
        );

        $this->createFile($path . '/Providers/RouteServiceProvider.php', $content);
    }

    /**
     * Add the routes files.
     */
    public function addRoutesFiles(string $name, string $slug, string $path): void
    {
        $controllers = 'app/Devices/' . $name . '/Http/Controllers';

        $contentApi = file_get_contents(__DIR__ . '/stubs/routes-api.stub');
        $contentApi = str_replace(['{{slug}}', '{{controllers_path}}'], [$slug, $controllers], $contentApi);

        $contentWeb = file_get_contents(__DIR__ . '/stubs/routes-web.stub');
        $contentWeb = str_replace(['{{slug}}', '{{controllers_path}}'], [$slug, $controllers], $contentWeb);

        $this->createFile($path . '/routes/api.php', $contentApi);
        $this->createFile($path . '/routes/web.php', $contentWeb);

        unset($contentApi, $contentWeb);
    }

    /**
     * Add the welcome view file.
     */
    public function addWelcomeViewFile(string $name): void
    {
        $path = resource_path('devices/' . $name);

        $this->createFile(
            $path . '/views/welcome.blade.php',
            file_get_contents(__DIR__ . '/stubs/welcome.blade.stub')
        );
    }

    public function createResourceDirectories(string $name): void
    {
        $path = resource_path('devices/' . lcfirst($name));
        foreach ($this->resourceDirectories as $directory) {
            $this->createDirectory($path . '/' . $directory);
        }
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/service.stub';
    }

    /**
     * Add the ModelFactory file.
     */
    public function addModelFactory(string $path): void
    {
        $modelFactory = file_get_contents(__DIR__ . '/stubs/model-factory.stub');
        $this->createFile($path . '/database/factories/ModelFactory.php', $modelFactory);

        unset($modelFactory);
    }
}
