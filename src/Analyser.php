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

use SebastianBergmann\FinderFacade\FinderFacade;
use SebastianBergmann\PHPLOC\Analyser as PHPLOCAnalyser;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 * @author Meletios Flevarakis <m.flevarakis@gmail.com>
 */
class Analyser
{
    use Finder;

    /**
     * @return array
     */
    public function analyse()
    {
        $analyser = new PHPLOCAnalyser();

        $finder = new FinderFacade([$this->findSourceRoot()]);
        $files  = $finder->findFiles();

        return $analyser->countFiles($files, true);
    }
}
