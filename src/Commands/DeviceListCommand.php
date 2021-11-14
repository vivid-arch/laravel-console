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
use Vivid\Console\Command;
use Vivid\Console\Finder;

class DeviceListCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    protected string $name = 'list:devices';
    protected string $description = 'List the devices in this project.';

    public function handle(): void
    {
        $devices = $this->listDevices()->all();

        $this->table(['Device', 'Slug', 'Path'], array_map(function ($device) {
            return [$device->name, $device->slug, $device->realPath];
        }, $devices));
    }
}
