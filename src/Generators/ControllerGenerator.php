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

use Exception;
use Vivid\Console\Str;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class ControllerGenerator extends Generator
{
    /**
     * @param string $name
     * @param string $device
     * @param bool   $isResource
     *
     * @throws Exception
     *
     * @return string
     */
    public function generate(string $name, string $device, bool $isResource = false, bool $invokable = false)
    {
        $name = Str::controller($name);
        $device = Str::device($device);

        $path = $this->findControllerPath($device, $name);

        if ($this->exists($path)) {
            throw new Exception("Controller $name already exists!");
            return;
        }

        $namespace = $this->findControllerNamespace($device);

        $content = file_get_contents($this->getStub($isResource));
        $content = str_replace(
            ['{{controller}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$name, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        return $this->relativeFromReal($path);
    }

    /**
     * Get the stub file for the generator.
     *
     * @param string $resource
     *
     * @return string
     */
    protected function getStub(string $resource)
    {
        if ($resource) {
            return __DIR__.'/stubs/controller.stub';
        }

        return __DIR__.'/stubs/controller.plain.stub';
    }
}
