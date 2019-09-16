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

namespace Vivid\Console\Generators;

use Vivid\Console\Finder;
use Vivid\Console\Filesystem;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class Generator
{
    use Finder;
    use Filesystem;
}
