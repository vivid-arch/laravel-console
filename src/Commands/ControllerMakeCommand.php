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

class ControllerMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    protected string $name = 'make:controller';
    protected string $description = 'Create a new Controller class in a Device';
    protected string $type = 'Controller';

    public function handle(): void
    {
        $generator = new ControllerGenerator();

        $device = $this->argument('device');
        $name = $this->argument('controller');

        $isResource = $this->option('resource');
        $isInvokable = $this->option('invokable');

        if ($isResource) {
            $stubType = 'resource';
        } elseif ($isInvokable) {
            $stubType = 'invokable';
        } else {
            $stubType = 'plain';
        }

        try {
            $controller = $generator->generate($name, $device, $stubType);

            $this->info(
                'Controller class created successfully.' .
                "\n" .
                "\n" .
                "Find it at <comment>$controller</comment> \n"
            );
            $this->info('Documentation: <comment>https://vivid-arch.github.io/docs/foundation/controllers/</comment>');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The controller\'s name.'],
            ['device', InputArgument::REQUIRED, 'The device in which the controller should be generated.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['resource', null, InputOption::VALUE_NONE, 'Generate a resource controller class.'],
            ['invokable', null, InputOption::VALUE_NONE, 'Generate a resource controller class.'],
        ];
    }

    /**
     * Parse the feature name.
     *  remove the Controller.php suffix if found
     *  we're adding it ourselves.
     */
    protected function parseName(string $name): string
    {
        return Str::studly(preg_replace('/Controller(\.php)?$/', '', $name) . 'Controller');
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        if ($this->option('plain')) {
            return __DIR__ . '/../Generators/stubs/controller.plain.stub';
        }

        return __DIR__ . '/../Generators/stubs/controller.stub';
    }
}
