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

use Vivid\Console\Components\Feature;
use Vivid\Console\Str;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class FeatureGenerator extends Generator
{
    /**
     * @throws \Exception
     */
    public function generate(string $feature, string $device): Feature
    {
        $feature = Str::feature($feature);
        $device = Str::device($device);

        $path = $this->findFeaturePath($device, $feature);

        if ($this->exists($path)) {
            throw new \Exception('Feature already exists!');
        }

        $namespace = $this->findFeatureNamespace($device);

        $content = file_get_contents($this->getStub());

        $content = str_replace(
            ['{{feature}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$feature, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        // generate test file
        $this->generateTestFile($feature, $device);

        return new Feature(
            $feature,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($device) ? $this->findDevice($device) : null,
            $content
        );
    }

    /**
     * Generate the test file.
     *
     * @throws \Exception
     */
    private function generateTestFile(string $feature, string $service)
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findFeatureTestNamespace($service);
        $featureNamespace = $this->findFeatureNamespace($service) . "\\$feature";
        $testClass = $feature . 'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{feature}}', '{{feature_namespace}}'],
            [$namespace, $testClass, mb_strtolower($feature), $featureNamespace],
            $content
        );

        $path = $this->findFeatureTestPath($service, $testClass);

        $this->createFile($path, $content);
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/feature.stub';
    }

    /**
     * Get the test stub file for the generator.
     */
    private function getTestStub(): string
    {
        return __DIR__ . '/stubs/feature-test.stub';
    }
}
