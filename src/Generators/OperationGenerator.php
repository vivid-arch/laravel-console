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

use Vivid\Console\Components\Operation;
use Vivid\Console\Str;

class OperationGenerator extends Generator
{
    /**
     * @throws \Exception
     */
    public function generate(string $operation, string $domain, bool $isQueueable = false, array $jobs = []): Operation
    {
        $operation = Str::operation($operation);
        $domain = Str::device($domain);

        $path = $this->findOperationPath($domain, $operation);

        if ($this->exists($path)) {
            throw new \Exception('Operation already exists!');
        }

        $namespace = $this->findOperationNamespace($domain);

        $content = file_get_contents($this->getStub($isQueueable));

        $useJobs = ''; // stores the `use` statements of the jobs
        $runJobs = ''; // stores the `$this->run` statements of the jobs

        foreach ($jobs as $index => $job) {
            $useJobs .= 'use ' . $job['namespace'] . '\\' . $job['className'] . ";\n";
            $runJobs .= "\t\t" . '$this->run(' . $job['className'] . '::class);';

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
        $this->generateTestFile($operation, $domain);

        return new Operation(
            $operation,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($domain) ? $this->findDevice($domain) : null,
            $content
        );
    }

    /**
     * Generate the test file.
     *
     * @throws \Exception
     */
    private function generateTestFile(string $operation, string $domain)
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findOperationTestNamespace($domain);
        $operationNamespace = $this->findOperationNamespace($domain) . "\\$operation";
        $testClass = $operation . 'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{operation}}', '{{operation_namespace}}'],
            [$namespace, $testClass, mb_strtolower($operation), $operationNamespace],
            $content
        );

        $path = $this->findOperationTestPath($domain, $testClass);

        $this->createFile($path, $content);
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(bool $isQueueable = false): string
    {
        if ($isQueueable) {
            $stubName = '/stubs/queueable-operation.stub';
        } else {
            $stubName = '/stubs/operation.stub';
        }

        return __DIR__ . $stubName;
    }

    /**
     * Get the test stub file for the generator.
     */
    private function getTestStub(): string
    {
        return __DIR__ . '/stubs/operation-test.stub';
    }
}
