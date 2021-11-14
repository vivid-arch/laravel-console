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

class Request extends Component
{
    public function __construct(
        string $title,
        string $service,
        string $namespace,
        string $file,
        string $path,
        string $relativePath,
        string $content
    ) {
        $this->setAttributes([
            'request'      => $title,
            'service'      => $service,
            'namespace'    => $namespace,
            'file'         => $file,
            'path'         => $path,
            'relativePath' => $relativePath,
            'content'      => $content,
        ]);
    }
}
