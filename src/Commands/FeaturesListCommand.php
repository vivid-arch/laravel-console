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

use Vivid\Console\Finder;
use Vivid\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class FeaturesListCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'list:features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the features.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        foreach ($this->listFeatures($this->argument('device')) as $device => $features) {
            $this->comment("\n$device\n");
            $features = array_map(function($feature) {
                return [$feature->title, $feature->service->name, $feature->file, $feature->realPath];
            }, $features->all());
            $this->table(['Feature', 'Device', 'File', 'Path'], $features);
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
            ['device', InputArgument::OPTIONAL, 'The device to list the features of.'],
        ];
    }
}
