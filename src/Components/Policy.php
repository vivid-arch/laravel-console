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
 * @author Bernat Jufré <info@behind.design>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 *
 * @property string $title
 * @property string $namespace
 * @property string $file
 * @property string $path
 * @property string $relativePath
 * @property string $content
 */
class Policy extends Component
{
    /**
     * Policy constructor.
     *
     * @param string $title
     * @param string $namespace
     * @param string $file
     * @param string $path
     * @param string $relativePath
     * @param string $content
     */
    public function __construct(
        string $title,
        string $namespace,
        string $file,
        string $path,
        string $relativePath,
        string $content
    ) {
        $this->setAttributes([
            'policy'       => $title,
            'namespace'    => $namespace,
            'file'         => $file,
            'path'         => $path,
            'relativePath' => $relativePath,
            'content'      => $content,
        ]);
    }
}
