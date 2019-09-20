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
use Vivid\Console\Components\Operation;
use Vivid\Console\Str;

/**
 * @author Ali Issa <ali@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class OperationGenerator extends Generator
{
    /**
     * @param string $operation
     * @param string $device
     * @param bool   $isQueueable
     * @param array  $jobs
     *
     * @throws Exception
     *
     * @return Operation
     */
    public function generate(string $operation, string $device, bool $isQueueable = false, array $jobs = [])
    {
        $operation = Str::operation($operation);
        $device = Str::device($device);

        $path = $this->findOperationPath($device, $operation);

        if ($this->exists($path)) {
            throw new Exception('Operation already exists!');
        }

        $namespace = $this->findOperationNamespace($device);

        $content = file_get_contents($this->getStub($isQueueable));

        $useJobs = ''; // stores the `use` statements of the jobs
        $runJobs = ''; // stores the `$this->run` statements of the jobs

        foreach ($jobs as $index => $job) {
            $useJobs .= 'use '.$job['namespace'].'\\'.$job['className'].";\n";
            $runJobs .= "\t\t".'$this->run('.$job['className'].'::class);';

            // only add carriage returns when it's not the last job
            if ($index != count($jobs) - 1) {
                $runJobs .= "\n\n";
            }
        }

        $content = str_replace(
            ['{{operation}}', '{{namespace}}', '{{foundation_namespace}}', '{{use_jobs}}', '{{run_jobs}}'],
            [$operation, $namespace, $this->findFoundationNamespace(), $useJobs, $runJobs],
            $content
        );

        $this->createFile($path, $content);

        // generate test file
        $this->generateTestFile($operation, $device);

        return new Operation(
            $operation,
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
     * @param string $operation
     * @param string $device
     *
     * @throws Exception
     */
    private function generateTestFile(string $operation, string $device)
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findOperationTestNamespace($device);
        $operationNamespace = $this->findOperationNamespace($device)."\\$operation";
        $testClass = $operation.'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{operation}}', '{{operation_namespace}}'],
            [$namespace, $testClass, mb_strtolower($operation), $operationNamespace],
            $content
        );

        $path = $this->findOperationTestPath($device, $testClass);

        $this->createFile($path, $content);
    }

    /**
     * Get the stub file for the generator.
     *
     * @param bool $isQueueable
     *
     * @return string
     */
    protected function getStub(bool $isQueueable = false)
    {
        if ($isQueueable) {
            $stubName = '/stubs/queueable-operation.stub';
        } else {
            $stubName = '/stubs/operation.stub';
        }

        return __DIR__.$stubName;
    }

    /**
     * Get the test stub file for the generator.
     *
     * @return string
     */
    private function getTestStub()
    {
        return __DIR__.'/stubs/operation-test.stub';
    }
}
