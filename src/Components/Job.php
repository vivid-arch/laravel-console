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
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 *
 * @property string $title
 * @property string $namespace
 * @property string $file
 * @property string $path
 * @property string $relativePath
 * @property Domain|null $domain
 * @property string $content
 */
class Job extends Component
{
    /**
     * Job constructor.
     *
     * @param string      $title
     * @param string      $namespace
     * @param string      $file
     * @param string      $path
     * @param string      $relativePath
     * @param Domain|null $domain
     * @param string      $content
     */
    public function __construct(
        string $title,
        string $namespace,
        string $file,
        string $path,
        string $relativePath,
        Domain $domain = null,
        string $content = ''
    ) {
        $className = str_replace(' ', '', $title).'Job';
        $this->setAttributes([
            'title'        => $title,
            'className'    => $className,
            'namespace'    => $namespace,
            'file'         => $file,
            'realPath'     => $path,
            'relativePath' => $relativePath,
            'domain'       => $domain,
            'content'      => $content,
        ]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();

        if ($attributes['domain'] instanceof Domain) {
            $attributes['domain'] = $attributes['domain']->toArray();
        }

        return $attributes;
    }
}
