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

/**
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class DeviceMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The base namespace for this command.
     *
     * @var string
     */
    private $namespace;

    /**
     * The Services path.
     *
     * @var string
     */
    private $path;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:device';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Device';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../Generators/stubs/service.stub';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        try {
            $name = $this->argument('name');
            //$hasAssets = $this->option('no-assets') ?? true;

            $generator = new DeviceGenerator();
            $device = $generator->generate($name, true);

            $this->info('Device '. $device->name ." created successfully. \n");

            $rootNamespace = $this->findRootNamespace();
            $serviceNamespace = $this->findDeviceNamespace($device->name);

            $serviceProvider = $serviceNamespace.'\\Providers\\'.$device->name.'ServiceProvider';

            $this->info(
                'Activate it by registering'.
                "<comment> $serviceProvider </comment> \n".
                "in <comment>/config/vivid.php</comment> inside the devices array with the following: \n"
            );

            $this->info("<comment>'$serviceProvider' => true,</comment> \n");
            $this->info("Documentation: <comment>https://vivid-arch.github.io/docs/foundation/devices/</comment> \n");
        } catch (\Exception $e) {
            $this->error($e->getMessage()."\n".$e->getFile().' at '.$e->getLine());
        }
    }

    public function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The service name.'],
        ];
    }

    public function getOptions()
    {
        return [
            ['type', null, InputOption::VALUE_REQUIRED, 'A device can be API-only or Web-only.'],
        ];
    }
}
