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
use Vivid\Console\Components\Request;
use Vivid\Console\Str;

/**
 * @author Bernat Jufré <info@behind.design>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class RequestGenerator extends Generator
{
    /**
     * Generate the file.
     *
     * @param string $name
     * @param string $service
     *
     * @throws Exception
     *
     * @return Request|bool
     */
    public function generate($name, $service)
    {
        $request = Str::request($name);
        $service = Str::device($service);
        $path = $this->findRequestPath($service, $request);

        if ($this->exists($path)) {
            throw new Exception('Request already exists');
        }

        $namespace = $this->findRequestsNamespace($service);

        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{request}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$request, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        return new Request(
            $request,
            $service,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            $content
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/../Generators/stubs/request.stub';
    }
}
