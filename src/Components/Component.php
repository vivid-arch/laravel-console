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

use Illuminate\Contracts\Support\Arrayable;

class Component implements Arrayable
{
    protected array $attributes = [];

    /**
     * Get the array representation of this instance.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes for this component.
     */
    protected function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Get an attribute's value if found.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }
}
