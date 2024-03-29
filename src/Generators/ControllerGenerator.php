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

class ControllerGenerator extends Generator
{
    /**
     * @throws Exception
     */
    public function generate(string $name, string $device, string $type = 'plain'): string
    {
        $name = Str::controller($name);
        $device = Str::device($device);

        $path = $this->findControllerPath($device, $name);

        if ($this->exists($path)) {
            throw new Exception("Controller $name already exists!");
        }

        $namespace = $this->findControllerNamespace($device);

        $content = file_get_contents($this->getStub($type));
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
     */
    protected function getStub(string $type): string
    {
        if ($type === 'resource') {
            return __DIR__ . '/stubs/controller.stub';
        }

        if ($type === 'invokable') {
            return __DIR__ . '/stubs/controller.invokable.stub';
        }

        return __DIR__ . '/stubs/controller.plain.stub';
    }
}
