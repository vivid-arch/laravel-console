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

use Vivid\Console\Command;
use Vivid\Console\Filesystem;
use Vivid\Console\Finder;
use Vivid\Console\Generators\OperationGenerator;
use Vivid\Console\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Ali Issa <ali@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class OperationMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:operation {--Q|queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Operation in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Operation';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $generator = new OperationGenerator();

        $device = Str::studly($this->argument('device'));
        $title = $this->parseName($this->argument('operation'));
        $isQueueable = $this->option('queue');
        try {
            $operation = $generator->generate($title, $device, $isQueueable);

            $this->info(
                'Operation class '.$title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$operation->relativePath.'</comment>'."\n"
            );
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments()
    {
        return [
            ['operation', InputArgument::REQUIRED, 'The operation\'s name.'],
            ['device', InputArgument::OPTIONAL, 'The device in which the operation should be implemented.'],
        ];
    }

    public function getOptions()
    {
        return [
            ['queue', 'Q', InputOption::VALUE_NONE, 'Whether a operation is queueable or not.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/../Generators/stubs/operation.stub';
    }

    /**
     * Parse the operation name.
     *  remove the Operation.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return Str::operation($name);
    }
}