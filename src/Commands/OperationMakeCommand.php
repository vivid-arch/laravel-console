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
use Vivid\Console\Generators\OperationGenerator;
use Vivid\Console\Str;

class OperationMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    protected string $name = 'make:operation {--Q|queue}';
    protected string $description = 'Create a new Operation';
    protected string $type = 'Operation';

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $generator = new OperationGenerator();

        $domain = '';
        //$domain = Str::studly($this->argument('domain'));
        $title = $this->parseName($this->argument('operation'));
        $isQueueable = $this->option('queue');

        try {
            $operation = $generator->generate($title, $domain, $isQueueable);

            $this->info(
                "Operation class $title created successfully." .
                "\n" .
                "\n" .
                "Find it at <comment> $operation->relativePath </comment> \n"
            );
            $this->info('Documentation: <comment>https://vivid-arch.github.io/docs/foundation/operations/</comment>');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments(): array
    {
        return [
            ['operation', InputArgument::REQUIRED, 'The operation\'s name.'],
        ];
    }

    public function getOptions(): array
    {
        return [
            ['queue', 'Q', InputOption::VALUE_NONE, 'Whether a operation is queueable or not.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     */
    public function getStub(): string
    {
        return __DIR__ . '/../Generators/stubs/operation.stub';
    }

    /**
     * Parse the operation name.
     *  remove the Operation.php suffix if found
     *  we're adding it ourselves.
     */
    protected function parseName(string $name): string
    {
        return Str::operation($name);
    }
}
