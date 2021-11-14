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
use Vivid\Console\Generators\JobGenerator;
use Vivid\Console\Str;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class JobMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    protected string $name = 'make:job {--Q|queue}';
    protected string $description = 'Create a new Job in a domain';
    protected string $type = 'Job';

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $generator = new JobGenerator();

        $domain = Str::studly($this->argument('domain'));
        $title = $this->parseName($this->argument('job'));
        $isQueueable = $this->option('queue');

        try {
            $job = $generator->generate($title, $domain, $isQueueable);

            $this->info(
                "Job class $title created successfully \n" .
                "\n" .
                "Find it at <comment> $job->relativePath </comment> \n"
            );
            $this->info('Documentation: <comment>https://vivid-arch.github.io/docs/foundation/jobs/</comment>');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments(): array
    {
        return [
            ['job', InputArgument::REQUIRED, 'The job\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain to be responsible for the job.'],
        ];
    }

    public function getOptions(): array
    {
        return [
            ['queue', 'Q', InputOption::VALUE_NONE, 'Whether a job is queueable or not.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
    {
        return __DIR__ . '/../Generators/stubs/job.stub';
    }

    /**
     * Parse the job name.
     *  remove the Job.php suffix if found
     *  we're adding it ourselves.
     */
    protected function parseName(string $name): string
    {
        return Str::job($name);
    }
}
