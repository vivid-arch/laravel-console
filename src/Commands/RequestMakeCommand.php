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
use Vivid\Console\Command;
use Vivid\Console\Filesystem;
use Vivid\Console\Finder;
use Vivid\Console\Generators\RequestGenerator;

class RequestMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    protected string $name = 'make:request';
    protected string $description = 'Create a Request in a specific device.';
    protected string $type = 'Request';

    public function handle(): void
    {
        $generator = new RequestGenerator();

        $name = $this->argument('request');
        $device = $this->argument('device');

        try {
            $request = $generator->generate($name, $device);

            $this->info(
                'Request class created successfully.' .
                "\n" .
                "\n" .
                "Find it at <comment> $request->relativePath </comment> \n"
            );
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments(): array
    {
        return [
            ['request', InputArgument::REQUIRED, 'The Request\'s name.'],
            ['device', InputArgument::REQUIRED, 'The Device\'s name.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     */
    public function getStub(): string
    {
        return __DIR__ . '/../Generators/stubs/request.stub';
    }
}
