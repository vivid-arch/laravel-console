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

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class Domain extends Component
{
    /**
     * Domain constructor.
     *
     * @param string $name
     * @param string $namespace
     * @param string $path
     * @param string $relativePath
     */
    public function __construct(
        string $name,
        string $namespace,
        string $path,
        string $relativePath
    ) {
        $this->setAttributes([
            'name'         => $name,
            'slug'         => Str::studly($name),
            'namespace'    => $namespace,
            'realPath'     => $path,
            'relativePath' => $relativePath,
        ]);
    }
}
