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

namespace Vivid\Console\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Vivid\Console\Command;
use Vivid\Console\Filesystem;
use Vivid\Console\Finder;
use Vivid\Console\Generators\DeviceGenerator;

class DeviceMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    private string $namespace;
    private string $path;
    protected string $name = 'make:device';
    protected string $description = 'Create a new Device';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../Generators/stubs/service.stub';
    }

    public function handle(): void
    {
        try {
            $name = $this->argument('name');
            $noAssets = $this->option('no-assets');

            $generator = new DeviceGenerator();
            $device = $generator->generate($name, $noAssets);

            $this->info('Device ' . $device->name . " created successfully. \n");

            $rootNamespace = $this->findRootNamespace();
            $serviceNamespace = $this->findDeviceNamespace($device->name);

            $serviceProvider = $serviceNamespace . '\\Providers\\' . $device->name . 'ServiceProvider';

            $this->info(
                'Activate it by registering' .
                "<comment> $serviceProvider </comment> \n" .
                "in <comment>/config/vivid.php</comment> inside the devices array with the following: \n"
            );

            $this->info("<comment>'$serviceProvider' => true,</comment> \n");
            $this->info("Documentation: <comment>https://vivid-arch.github.io/docs/foundation/devices/</comment> \n");
        } catch (\Exception $e) {
            $this->error($e->getMessage() . "\n" . $e->getFile() . ' at ' . $e->getLine());
        }
    }

    public function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The service name.'],
        ];
    }

    public function getOptions(): array
    {
        return [
            ['no-assets', null, InputOption::VALUE_NONE, 'Specify if a Device has Assets.'],
        ];
    }
}
