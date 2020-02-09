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
use Vivid\Console\Generators\FeatureGenerator;
use Vivid\Console\Str;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class FeatureMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Feature in a device';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Feature';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle()
    {
        try {
            $service = Str::studly($this->argument('device'));
            $title = $this->parseName($this->argument('feature'));

            $generator = new FeatureGenerator();
            $feature = $generator->generate($title, $service);

            $this->info(
                "Feature class $feature->title created successfully." .
                "\n" .
                "\n" .
                "Find it at <comment> $feature->relativePath </comment> \n"
            );
            $this->info("Documentation: <comment>https://vivid-arch.github.io/docs/foundation/features/</comment>");

        } catch (\Exception $e) {
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
            ['feature', InputArgument::REQUIRED, 'The feature\'s name.'],
            ['device', InputArgument::OPTIONAL, 'The device in which the feature should be implemented.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../Generators/stubs/feature.stub';
    }

    /**
     * Parse the feature name.
     *  remove the Feature.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return Str::feature($name);
    }
}
