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

namespace Vivid\Console\Components;

use Illuminate\Support\Str;

class Device extends Component
{
    public function __construct(string $name, string $realPath, string $relativePath)
    {
        $this->setAttributes([
            'name'         => $name,
            'slug'         => Str::snake($name),
            'realPath'     => $realPath,
            'relativePath' => $relativePath,
        ]);
    }
}
