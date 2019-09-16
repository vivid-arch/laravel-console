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

/**
 * @author Ali Issa <ali@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class Operation extends Component
{

    /**
     * Operation constructor.
     * @param $title
     * @param $file
     * @param $realPath
     * @param $relativePath
     * @param Device|null $service
     * @param string $content
     */
    public function __construct(
        string $title,
        string $file,
        string $realPath,
        string $relativePath,
        Device $service = null,
        string $content = ''
    ) {
        $className = str_replace(' ', '', $title).'Operation';

        $this->setAttributes([
            'title' => $title,
            'className' => $className,
            'service' => $service,
            'file' => $file,
            'realPath' => $realPath,
            'relativePath' => $relativePath,
            'content' => $content,
        ]);
    }
}