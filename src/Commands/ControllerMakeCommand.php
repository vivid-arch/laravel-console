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
use Vivid\Console\Generators\ControllerGenerator;
use Vivid\Console\Str;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class ControllerMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Controller class in a device';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle()
    {
        $generator = new ControllerGenerator();

        $device = $this->argument('device');
        $name = $this->argument('controller');
        $isResource = $this->option('resource');

        try {
            $controller = $generator->generate($name, $device, $isResource);

            $this->info(
                "Controller class created successfully." .
                "\n" .
                "\n" .
                "Find it at <comment>$controller</comment> \n"
            );
            $this->info("Documentation: <comment>https://vivid-arch.github.io/docs/foundation/controllers/</comment>");

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The controller\'s name.'],
            ['device', InputArgument::REQUIRED, 'The device in which the controller should be generated.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['resource', null, InputOption::VALUE_NONE, 'Generate a resource controller class.'],
        ];
    }

    /**
     * Parse the feature name.
     *  remove the Controller.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return Str::studly(preg_replace('/Controller(\.php)?$/', '', $name).'Controller');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('plain')) {
            return __DIR__.'/../Generators/stubs/controller.plain.stub';
        }

        return __DIR__.'/../Generators/stubs/controller.stub';
    }
}
