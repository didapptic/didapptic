<?php
declare(strict_types=1);
/**
 * MIT License
 *
 * Copyright (c) 2020 didapptic, <info@didapptic.com>
 *
 * @author Dogan Ucar <dogan@dogan-ucar.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Didapptic\Service\Installation;

use Didapptic\Didapptic;
use Didapptic\Service\Installation\Step\IStep;
use Didapptic\Service\Installation\Step\MissingDirs;
use Didapptic\Service\Installation\Step\MissingFiles;
use Didapptic\Service\Installation\Step\NonWritableFiles;
use Didapptic\Service\Installation\Step\PropertyFile;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class Installer
 *
 * @package Didapptic\Service\Installation
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Installer {

    /** @var string */
    private $appRoot;

    /**
     * Installer constructor.
     *
     * @param string $appRoot
     */
    public function __construct(string $appRoot) {
        $this->appRoot = $appRoot;
    }

    public function isInstalled(): bool {

        $steps = new ArrayList();
        $steps->add(
            Didapptic::getServer()->query(MissingDirs::class)
        );

        $steps->add(
            Didapptic::getServer()->query(MissingFiles::class)
        );

        $steps->add(
            Didapptic::getServer()->query(NonWritableFiles::class)
        );

        $steps->add(
            Didapptic::getServer()->query(PropertyFile::class)
        );


        /** @var IStep $step */
        foreach ($steps as $step) {
            if (false === $step->isValid()) return false;
        }

        return true;
    }

}
