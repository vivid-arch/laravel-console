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

class FeatureMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    protected string $name = 'make:feature';
    protected string $description = 'Create a new Feature in a device';
    protected string $type = 'Feature';

    public function handle(): void
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
            $this->info('Documentation: <comment>https://vivid-arch.github.io/docs/foundation/features/</comment>');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function getArguments(): array
    {
        return [
            ['feature', InputArgument::REQUIRED, 'The feature\'s name.'],
            ['device', InputArgument::OPTIONAL, 'The device in which the feature should be implemented.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../Generators/stubs/feature.stub';
    }

    /**
     * Parse the feature name.
     *  remove the Feature.php suffix if found
     *  we're adding it ourselves.
     */
    protected function parseName(string $name): string
    {
        return Str::feature($name);
    }
}
