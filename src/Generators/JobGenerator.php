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

use Vivid\Console\Components\Job;
use Vivid\Console\Str;

class JobGenerator extends Generator
{
    /**
     * @throws \Exception
     */
    public function generate(string $job, string $domain, bool $isQueueable = false): Job
    {
        $job = Str::job($job);
        $domain = Str::domain($domain);
        $path = $this->findJobPath($domain, $job);

        if ($this->exists($path)) {
            throw new \Exception('Job already exists');
        }

        // Make sure the domain directory exists
        $this->createDomainDirectory($domain);

        // Create the job
        $namespace = $this->findDomainJobsNamespace($domain);

        $content = file_get_contents($this->getStub($isQueueable));
        $content = str_replace(
            ['{{job}}', '{{namespace}}', '{{foundation_namespace}}'],
            [$job, $namespace, $this->findFoundationNamespace()],
            $content
        );

        $this->createFile($path, $content);

        $this->generateTestFile($job, $domain);

        return new Job(
            $job,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            $this->findDomain($domain),
            $content
        );
    }

    /**
     * Generate test file.
     *
     * @throws \Exception
     */
    private function generateTestFile(string $jobName, string $domain)
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findDomainJobsTestsNamespace($domain);
        $jobNamespace = $this->findDomainJobsNamespace($domain) . "\\$jobName";
        $testClass = $jobName . 'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{job}}', '{{job_namespace}}'],
            [$namespace, $testClass, Str::snake($jobName), $jobNamespace],
            $content
        );

        $path = $this->findJobTestPath($domain, $testClass);

        $this->createFile($path, $content);
    }

    /**
     * Create domain directory.
     */
    private function createDomainDirectory(string $domain)
    {
        $this->createDirectory($this->findDomainPath($domain) . '/Jobs');
        $this->createDirectory($this->findDomainTestsPath($domain) . '/Jobs');
    }

    /**
     * Get the stub file for the generator.
     */
    public function getStub(bool $isQueueable = false): string
    {
        if ($isQueueable) {
            $stubName = '/stubs/queueable-job.stub';
        } else {
            $stubName = '/stubs/job.stub';
        }

        return __DIR__ . $stubName;
    }

    /**
     * Get the test stub file for the generator.
     */
    public function getTestStub(): string
    {
        return __DIR__ . '/stubs/job-test.stub';
    }
}
