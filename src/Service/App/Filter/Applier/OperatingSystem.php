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

class OperatingSystem implements IFilterApplier {

    /** @var array */
    private $arguments;

    public function __construct(array $arguments) {
        $this->arguments = $arguments;
    }

    public function filter(ArrayList $apps): ArrayList {
        $newList = new ArrayList();

        // base case: no filter criteria
        if (0 === $this->getArgumentSize()) {
            return $apps;
        }

        // start filtering
        /** @var App $app */
        foreach ($apps as $app) {

            if (
                true === in_array(
                    $app->getOperatingSystem()
                    , $this->arguments
                )
            ) {
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
