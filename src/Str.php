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

use Illuminate\Support\Str as LaravelStr;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class Str extends LaravelStr
{
    /**
     * Determine the real name of the given name,
     * excluding the given pattern.
     * 	i.e. the name: "CreateArticleFeature.php" with pattern '/Feature.php'
     * 		will result in "Create Article".
     *
     * @param string $name
     * @param string $pattern
     *
     * @return string
     */
    public static function realName($name, $pattern = '//')
    {
        $name = preg_replace($pattern, '', $name);

        return implode(' ', preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Get the given name formatted as a feature.
     *
     * 	i.e. "Create Post Feature", "CreatePostFeature.php", "createPost", "createe"
     * 	and many other forms will be transformed to "CreatePostFeature" which is
     * 	the standard feature class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function feature($name)
    {
        return static::studly(preg_replace('/Feature(\.php)?$/', '', $name).'Feature');
    }

    /**
     * Get the given name formatted as a job.
     *
     * 	i.e. "Create Post Feature", "CreatePostJob.php", "createPost",
     * 	and many other forms will be transformed to "CreatePostJob" which is
     * 	the standard job class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function job($name)
    {
        return static::studly(preg_replace('/Job(\.php)?$/', '', $name).'Job');
    }

    /**
     * Get the given name formatted as an operation.
     *
     *  i.e. "Create Post Operation", "CreatePostOperation.php", "createPost",
     *  and many other forms will be transformed to "CreatePostOperation" which is
     *  the standard operation class name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function operation($name)
    {
        return static::studly(preg_replace('/Operation(\.php)?$/', '', $name).'Operation');
    }

    /**
     * Get the given name formatted as a domain.
     *
     * Domain names are just CamelCase
     *
     * @param string $name
     *
     * @return string
     */
    public static function domain($name)
    {
        return static::studly($name);
    }

    /**
     * Get the given name formatted as a device name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function device($name)
    {
        return static::studly($name);
    }

    /**
     * Get the given name formatted as a controller name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function controller($name)
    {
        return static::studly(preg_replace('/Controller(\.php)?$/', '', $name).'Controller');
    }

    /**
     * Get the given name formatted as a model.
     *
     * Model names are just CamelCase
     *
     * @param string $name
     *
     * @return string
     */
    public static function model($name)
    {
        return static::studly($name);
    }

    /**
     * Get the given name formatted as a policy.
     *
     * @param $name
     *
     * @return string
     */
    public static function policy($name)
    {
        return static::studly(preg_replace('/Policy(\.php)?$/', '', $name).'Policy');
    }

    /**
     * Get the given name formatted as a request.
     *
     * @param $name
     *
     * @return string
     */
    public static function request($name)
    {
        return static::studly(preg_replace('/Request(\.php)?$/', '', $name).'Request');
    }
}
