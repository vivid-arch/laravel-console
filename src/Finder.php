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

namespace Vivid\Console;

use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Vivid\Console\Components\Device;
use Vivid\Console\Components\Domain;
use Vivid\Console\Components\Feature;
use Vivid\Console\Components\Job;

define('DS', DIRECTORY_SEPARATOR);

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
trait Finder
{
    /**
     * The name of the source directory.
     *
     * @var string
     */
    protected $srcDirectoryName = 'app';

    public function fuzzyFind($query)
    {
        $finder = new SymfonyFinder();

        $files = $finder->in($this->findDevicesRootPath().'/*/Features') // features
            ->in($this->findDomainsRootPath().'/*/Jobs') // jobs
            ->name('*.php')
            ->files();

        $matches = [
            'jobs'     => [],
            'features' => [],
        ];

        foreach ($files as $file) {
            $base = $file->getBaseName();
            $name = str_replace(['.php', ' '], '', $base);

            $query = str_replace(' ', '', trim($query));

            similar_text($query, mb_strtolower($name), $percent);

            if ($percent > 35) {
                if (strpos($base, 'Feature.php')) {
                    $matches['features'][] = [$this->findFeature($name)->toArray(), $percent];
                } elseif (strpos($base, 'Job.php')) {
                    $matches['jobs'][] = [$this->findJob($name)->toArray(), $percent];
                }
            }
        }

        // sort the results by their similarity percentage
        $this->sortFuzzyResults($matches['jobs']);
        $this->sortFuzzyResults($matches['features']);

        $matches['features'] = $this->mapFuzzyResults($matches['features']);
        $matches['jobs'] = array_map(function ($result) {
            return $result[0];
        }, $matches['jobs']);

        return $matches;
    }

    /**
     * Sort the fuzzy-find results.
     *
     * @param array &$results
     *
     * @return bool
     */
    private function sortFuzzyResults(&$results)
    {
        return usort($results, function ($resultLeft, $resultRight) {
            return $resultLeft[1] < $resultRight[1];
        });
    }

    /**
     * Map the fuzzy-find results into the data
     * that should be returned.
     *
     * @param  array $results
     *
     * @return array
     */
    private function mapFuzzyResults($results)
    {
        return array_map(function ($result) {
            return $result[0];
        }, $results);
    }

    /**
     * Get the source directory name.
     * In a microservice installation this will be `app`. `src` otherwise.
     *
     * @return string
     */
    public function getSourceDirectoryName()
    {
        if (file_exists(base_path().'/'.$this->srcDirectoryName)) {
            return $this->srcDirectoryName;
        }

        return 'app';
    }

    /**
     * Determines whether this is a vivid microservice installation.
     *
     * @return bool
     */
    public function isMicroservice()
    {
        return !($this->getSourceDirectoryName() === $this->srcDirectoryName);
    }

    /**
     * Get the namespace used for the application.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function findRootNamespace()
    {
        // read composer.json file contents to determine the namespace
        $composer = json_decode(file_get_contents(base_path().'/composer.json'), true);

        // see which one refers to the "src/" directory
        foreach ($composer['autoload']['psr-4'] as $namespace => $directory) {
            if ($directory === $this->getSourceDirectoryName().'/') {
                return trim($namespace, '\\');
            }
        }

        throw new Exception('App namespace not set in composer.json');
    }

    /**
     * Find the namespace of the foundation.
     *
     * @return string
     */
    public function findFoundationNamespace()
    {
        return 'Vivid\Foundation';
    }

    /**
     * Find the namespace for the given service name.
     *
     * @param string $device
     *
     * @throws Exception
     *
     * @return string
     */
    public function findDeviceNamespace($device)
    {
        $root = $this->findRootNamespace();

        return (!$device) ? $root : "$root\\Devices\\$device";
    }

    /**
     * get the root of the source directory.
     *
     * @return string
     */
    public function findSourceRoot()
    {
        return ($this->isMicroservice()) ? app_path() : base_path().'/'.$this->srcDirectoryName;
    }

    /**
     * Find the root path of all the devices.
     *
     * @return string
     */
    public function findDevicesRootPath()
    {
        return $this->findSourceRoot().'/Devices';
    }

    /**
     * Find the path to the directory of the given device name.
     * In the case of a microservice service installation this will be app path.
     *
     * @param string $device
     *
     * @return string
     */
    public function findDevicePath($device)
    {
        return (!$device) ? app_path() : $this->findDevicesRootPath()."/$device";
    }

    /**
     * Find the features root path in the given device.
     *
     * @param string $device
     *
     * @return string
     */
    public function findFeaturesRootPath($device)
    {
        return $this->findDevicePath($device).'/Features';
    }

    /**
     * Find the file path for the given feature.
     *
     * @param string $device
     * @param string $feature
     *
     * @return string
     */
    public function findFeaturePath($device, $feature)
    {
        return $this->findFeaturesRootPath($device)."/$feature.php";
    }

    /**
     * Find the test file path for the given feature.
     *
     * @param string $device
     * @param string $feature
     *
     * @return string
     */
    public function findFeatureTestPath($device, $test)
    {
        $root = ($device) ? $this->findDevicePath($device).'/Tests' : base_path().'/tests';

        return "$root/Features/$test.php";
    }

    /**
     * Find the namespace for features in the given device.
     *
     * @param string $device
     *
     * @throws Exception
     *
     * @return string
     */
    public function findFeatureNamespace($device)
    {
        return $this->findDeviceNamespace($device).'\\Features';
    }

    /**
     * Find the namespace for features tests in the given device.
     *
     * @param string $device
     *
     * @throws Exception
     *
     * @return string
     */
    public function findFeatureTestNamespace($device)
    {
        return $this->findDeviceNamespace($device).'\\Tests\\Features';
    }

    /**
     * Find the operations root path in the given device.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findOperationsRootPath($domain)
    {
        return $this->findSourceRoot().'/Operations';
    }

    /**
     * Find the file path for the given operation.
     *
     * @param string $device
     * @param string $operation
     *
     * @return string
     */
    public function findOperationPath($device, $operation)
    {
        return $this->findOperationsRootPath($device)."/$operation.php";
    }

    /**
     * Find the test file path for the given operation.
     *
     * @param string $device
     * @param $test
     *
     * @return string
     */
    public function findOperationTestPath($device, $test)
    {
        $root = ($device) ? $this->findDevicePath($device).'/Tests' : base_path().'/tests';

        return "$root/Operations/$test.php";
    }

    /**
     * Find the namespace for operations.
     *
     * @throws Exception
     *
     * @return string
     */
    public function findOperationNamespace()
    {
        return $this->findRootNamespace().'\\Operations';
    }

    /**
     * Find the namespace for operations tests in the given device.
     *
     * @param string $device
     *
     * @throws Exception
     *
     * @return string
     */
    public function findOperationTestNamespace($device)
    {
        return $this->findOperationNamespace().'\\Tests';
    }

    /**
     * Find the root path of domains.
     *
     * @return string
     */
    public function findDomainsRootPath()
    {
        return $this->findSourceRoot().'/Domains';
    }

    /**
     * Find the path for the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findDomainPath($domain)
    {
        return $this->findDomainsRootPath()."/$domain";
    }

    /**
     * Get the list of domains.
     *
     * @return \Illuminate\Support\Collection;
     */
    public function listDomains()
    {
        $finder = new SymfonyFinder();
        $directories = $finder
            ->depth(0)
            ->in($this->findDomainsRootPath())
            ->directories();

        $domains = new Collection();
        foreach ($directories as $directory) {
            $name = $directory->getRelativePathName();

            $domain = new Domain(
                Str::realName($name),
                $this->findDomainNamespace($name),
                $directory->getRealPath(),
                $this->relativeFromReal($directory->getRealPath())
            );

            $domains->push($domain);
        }

        return $domains;
    }

    /**
     * List the jobs per domain,
     * optionally provide a domain name to list its jobs.
     *
     * @param string $domainName
     *
     * @throws Exception
     *
     * @return Collection
     */
    public function listJobs($domainName = null)
    {
        $domains = ($domainName) ? [$this->findDomain(Str::domain($domainName))] : $this->listDomains();

        $jobs = new Collection();
        foreach ($domains as $domain) {
            $path = $domain->realPath;

            $finder = new SymfonyFinder();
            $files = $finder
                ->name('*Job.php')
                ->in($path.'/Jobs')
                ->files();

            $jobs[$domain->name] = new Collection();

            foreach ($files as $file) {
                $name = $file->getRelativePathName();
                $job = new Job(
                    Str::realName($name, '/Job.php/'),
                    $this->findDomainJobsNamespace($domain->name),
                    $name,
                    $file->getRealPath(),
                    $this->relativeFromReal($file->getRealPath()),
                    $domain,
                    file_get_contents($file->getRealPath())
                );

                $jobs[$domain->name]->push($job);
            }
        }

        return $jobs;
    }

    /**
     * Find the path for the given job name.
     *
     * @param  string$domain
     * @param  string$job
     *
     * @return string
     */
    public function findJobPath($domain, $job)
    {
        return $this->findDomainPath($domain).DS.'Jobs'.DS.$job.'.php';
    }

    /**
     * Find the namespace for the given domain.
     *
     * @param string $domain
     *
     * @throws Exception
     *
     * @return string
     */
    public function findDomainNamespace($domain)
    {
        return $this->findRootNamespace().'\\Domains\\'.$domain;
    }

    /**
     * Find the namespace for the given domain's Jobs.
     *
     * @param string $domain
     *
     * @throws Exception
     *
     * @return string
     */
    public function findDomainJobsNamespace($domain)
    {
        return $this->findDomainNamespace($domain).'\Jobs';
    }

    /**
     * Find the namespace for the given domain's Jobs.
     *
     * @param string $domain
     *
     * @throws Exception
     *
     * @return string
     */
    public function findDomainJobsTestsNamespace($domain)
    {
        return $this->findDomainNamespace($domain).'\Tests\Jobs';
    }

    /**
     * Get the path to the tests of the given domain.
     *
     * @param string $domain
     *
     * @return string
     */
    public function findDomainTestsPath($domain)
    {
        if ($this->isMicroservice()) {
            return base_path().DS.'tests'.DS.'Domains'.DS.$domain;
        }

        return $this->findDomainPath($domain).DS.'Tests';
    }

    /**
     * Find the test path for the given job.
     *
     * @param string $domain
     * @param string $jobTest
     *
     * @return string
     */
    public function findJobTestPath($domain, $jobTest)
    {
        return $this->findDomainTestsPath($domain).DS.'Jobs'.DS.$jobTest.'.php';
    }

    /**
     * Find the path for the give controller class.
     *
     * @param string $device
     * @param string $controller
     *
     * @return string
     */
    public function findControllerPath($device, $controller)
    {
        return $this->findDevicePath($device).'/Http/Controllers/'.$controller.'.php';
    }

    /**
     * Find the namespace of controllers in the given device.
     *
     * @param string $device
     *
     * @throws Exception
     *
     * @return string
     */
    public function findControllerNamespace($device)
    {
        return $this->findDeviceNamespace($device).'\\Http\\Controllers';
    }

    /**
     * Get the list of devices.
     *
     * @return \Illuminate\Support\Collection
     */
    public function listDevices()
    {
        $devices = new Collection();

        if (file_exists($this->findDevicesRootPath())) {
            $finder = new SymfonyFinder();

            foreach ($finder->directories()->depth('== 0')->in($this->findDevicesRootPath())->directories() as $dir) {
                $realPath = $dir->getRealPath();
                $devices->push(new Device($dir->getRelativePathName(), $realPath, $this->relativeFromReal($realPath)));
            }
        }

        return $devices;
    }

    /**
     * Find the service for the given service name.
     *
     * @param string $device
     *
     * @throws Exception
     *
     * @return \Vivid\Console\Components\Device
     */
    public function findDevice($device)
    {
        $finder = new SymfonyFinder();
        $dirs = $finder->name($device)->in($this->findDevicesRootPath())->directories();
        if ($dirs->count() < 1) {
            throw new Exception('Service "'.$device.'" could not be found.');
        }

        foreach ($dirs as $dir) {
            $path = $dir->getRealPath();

            return  new Device(Str::device($device), $path, $this->relativeFromReal($path));
        }
    }

    /**
     * Find the domain for the given domain name.
     *
     * @param string $domain
     *
     * @throws Exception
     *
     * @return \Vivid\Console\Components\Domain
     */
    public function findDomain($domain)
    {
        $finder = new SymfonyFinder();
        $dirs = $finder->name($domain)->in($this->findDomainsRootPath())->directories();
        if ($dirs->count() < 1) {
            throw new Exception('Domain "'.$domain.'" could not be found.');
        }

        foreach ($dirs as $dir) {
            $path = $dir->getRealPath();

            return  new Domain(
                Str::device($domain),
                $this->findDomainNamespace($domain),
                $path,
                $this->relativeFromReal($path)
            );
        }
    }

    /**
     * Find the feature for the given feature name.
     *
     * @param string $name
     *
     * @throws Exception
     *
     * @return \Vivid\Console\Components\Feature
     */
    public function findFeature($name)
    {
        $name = Str::feature($name);
        $fileName = "$name.php";

        $finder = new SymfonyFinder();
        $files = $finder->name($fileName)->in($this->findDevicesRootPath())->files();
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $serviceName = strstr($file->getRelativePath(), DS, true);
            $device = $this->findDevice($serviceName);
            $content = file_get_contents($path);

            return new Feature(
                Str::realName($name, '/Feature/'),
                $fileName,
                $path,
                $this->relativeFromReal($path),
                $device,
                $content
            );
        }
    }

    /**
     * Find the feature for the given feature name.
     *
     * @param string $name
     *
     * @throws Exception
     *
     * @return \Vivid\Console\Components\Job
     */
    public function findJob($name)
    {
        $name = Str::job($name);
        $fileName = "$name.php";

        $finder = new SymfonyFinder();
        $files = $finder->name($fileName)->in($this->findDomainsRootPath())->files();
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $domainName = strstr($file->getRelativePath(), DIRECTORY_SEPARATOR, true);
            $domain = $this->findDomain($domainName);
            $content = file_get_contents($path);

            return new Job(
                Str::realName($name, '/Job/'),
                $this->findDomainJobsNamespace($domainName),
                $fileName,
                $path,
                $this->relativeFromReal($path),
                $domain,
                $content
            );
        }
    }

    /**
     * Get the list of features,
     * optionally withing a specified service.
     *
     * @param string $serviceName
     *
     * @throws \Exception
     *
     * @return \Illuminate\Support\Collection
     */
    public function listFeatures($serviceName = '')
    {
        $services = $this->listDevices();

        if (!empty($serviceName)) {
            $services = $services->filter(function ($service) use ($serviceName) {
                return $service->name === $serviceName || $service->slug === $serviceName;
            });

            if ($services->isEmpty()) {
                throw new InvalidArgumentException('Service "'.$serviceName.'" could not be found.');
            }
        }

        $features = [];
        foreach ($services as $service) {
            $serviceFeatures = new Collection();
            $finder = new SymfonyFinder();
            $files = $finder
                ->name('*Feature.php')
                ->in($this->findFeaturesRootPath($service->name))
                ->files();
            foreach ($files as $file) {
                $fileName = $file->getRelativePathName();
                $title = Str::realName($fileName, '/Feature.php/');
                $realPath = $file->getRealPath();
                $relativePath = $this->relativeFromReal($realPath);

                $serviceFeatures->push(new Feature($title, $fileName, $realPath, $relativePath, $service));
            }

            // add to the features array as [service_name => Collection(Feature)]
            $features[$service->name] = $serviceFeatures;
        }

        return $features;
    }

    /**
     * Get the path to the passed model.
     *
     * @param string $model
     *
     * @return string
     */
    public function findModelPath($model)
    {
        return $this->getSourceDirectoryName().'/Data/'.$model.'.php';
    }

    /**
     * Get the path to the policies directory.
     *
     * @return string
     */
    public function findPoliciesPath()
    {
        return $this->getSourceDirectoryName().'/Policies';
    }

    /**
     * Get the path to the passed policy.
     *
     * @param string $policy
     *
     * @return string
     */
    public function findPolicyPath($policy)
    {
        return $this->findPoliciesPath().'/'.$policy.'.php';
    }

    /**
     * Get the path to the request directory of a specific service.
     *
     * @param string $service
     *
     * @return string
     */
    public function findRequestsPath($service)
    {
        return $this->findDevicePath($service).'/Http/Requests';
    }

    /**
     * Get the path to a specific request.
     *
     * @param string $service
     * @param string $request
     *
     * @return string
     */
    public function findRequestPath($service, $request)
    {
        return $this->findRequestsPath($service).'/'.$request.'.php';
    }

    /**
     * Get the namespace for the Models.
     *
     * @throws Exception
     *
     * @return string
     */
    public function findModelNamespace()
    {
        return $this->findRootNamespace().'\\Data';
    }

    /**
     * Get the namespace for Policies.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function findPolicyNamespace()
    {
        return $this->findRootNamespace().'\\Policies';
    }

    /**
     * Get the requests namespace for the service passed in.
     *
     * @param string $device
     *
     * @throws Exception
     *
     * @return string
     */
    public function findRequestsNamespace($device)
    {
        return $this->findDeviceNamespace($device).'\\Http\\Requests';
    }

    /**
     * Get the relative version of the given real path.
     *
     * @param string $path
     * @param string $needle
     *
     * @return string
     */
    protected function relativeFromReal($path, $needle = '')
    {
        if (!$needle) {
            $needle = $this->getSourceDirectoryName().'/';
        }

        return strstr($path, $needle);
    }

    /**
     * Get the path to the Composer.json file.
     *
     * @return string
     */
    protected function getComposerPath()
    {
        return app()->basePath().'/composer.json';
    }

    /**
     * Get the path to the given configuration file.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getConfigPath($name)
    {
        return app()['path.config'].'/'.$name.'.php';
    }
}
