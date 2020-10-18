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

namespace Didapptic\Service\App\Filter\Applier;

use Didapptic\Object\App;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;

/**
 * Class Text
 *
 * @package Didapptic\Service\App\Filter\Applier
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Text implements IFilterApplier {

    /** @var array */
    private $arguments;

    /**
     * Text constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        $this->arguments = $arguments;
    }

    /**
     * @param ArrayList $apps
     *
     * @return ArrayList
     */
    public function filter(ArrayList $apps): ArrayList {
        $newList = new ArrayList();

        // base case: no arguments
        if (0 === $this->getArgumentSize()) {
            return $apps;
        }

        $argument = $this->arguments[0]; // there is only one text field in UI
        $argument = strtolower($argument);

        // base case 1: empty string
        if ("" === trim($argument)) {
            return $apps;
        }

        /** @var App $app */
        foreach ($apps as $app) {
            $appName = strtolower($app->getName());

            if (false !== strpos($appName, $argument)) {
                $newList->add($app);
            }

        }
        return $newList;
    }

    /**
     * @return int
     */
    public function getArgumentSize(): int {
        return count($this->arguments);
    }

}
