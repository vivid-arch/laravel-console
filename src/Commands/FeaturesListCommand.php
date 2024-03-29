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
use Vivid\Console\Finder;

class FeaturesListCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    protected string $name = 'list:features';
    protected string $description = 'List the features.';

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        foreach ($this->listFeatures($this->argument('device')) as $device => $features) {
            $this->comment("\n$device\n");
            $features = array_map(function ($feature) {
                return [$feature->title, $feature->service->name, $feature->file, $feature->realPath];
            }, $features->all());
            $this->table(['Feature', 'Device', 'File', 'Path'], $features);
        }
    }

    protected function getArguments(): array
    {
        return [
            ['device', InputArgument::OPTIONAL, 'The device to list the features of.'],
        ];
    }
}
