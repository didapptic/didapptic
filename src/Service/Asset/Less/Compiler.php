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

namespace Didapptic\Service\Asset\Less;

use Didapptic\Didapptic;
use Didapptic\Object\Constant\CSS;
use Didapptic\Object\Constant\Extension;
use Didapptic\Object\Environment;
use lessc;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class Compiler
 *
 * Wraps an open source less compiler for less based stylesheet files
 *
 * @package Didapptic\Service\Asset\Less
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Compiler {

    /** @var lessc */
    private $less;

    /** @var Environment */
    private $environment;

    /**
     * Compiler constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment) {
        $this->less        = new lessc();
        $this->environment = $environment;
        $this->setUp();
    }

    private function setUp(): void {
        $this->less->setPreserveComments(true);
        $this->setUpForProduction();
    }

    private function setUpForProduction(): void {
        if (true === $this->environment->isDebug()) return;
        $this->less->setFormatter("compressed");
    }

    /**
     * compiles all stylesheet files defined in the configs
     */
    public function compileAll(): void {
        $iteratorIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                Didapptic::getServer()->getStylesheetPath()
            )
        );

        /** @var SplFileInfo $info */
        foreach ($iteratorIterator as $info) {

            $fileName = strtolower($info->getBasename('.' . $info->getExtension()));
            if (
                true === $info->isFile()
                && Extension::LESS === $info->getExtension()
                && CSS::GENERIC_NAME_STYLE === $fileName
            ) {

                $this->less->checkedCompile(
                    $info->getRealPath()
                    , Didapptic::getServer()->getStylesheetDistPath() . strtolower($info->getPathInfo()->getFilename() . "." . Extension::CSS)
                );
            }

        }
    }

}
