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

use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

interface IFilterApplier {

    public const TYPE_OPERATING_SYSTEM = "system.operating.type";
    public const TYPE_SUBJECT          = "subject.type";
    public const TYPE_CATEGORY         = "category.type";
    public const TYPE_TEXT             = "text.type";

    /**
     * IFilterApplier constructor.
     *
     * Gets the arguments that are used to filter apps
     *
     * @param array $arguments
     */
    public function __construct(array $arguments);

    /**
     * Filters the apps based on the arguments injected in the constructor.
     * The result is a new list with the filtered app data
     *
     * @param ArrayList $apps
     *
     * @return ArrayList
     */
    public function filter(ArrayList $apps): ArrayList;

    /**
     * returns the number of filter arguments
     *
     * @return int
     */
    public function getArgumentSize(): int;

}
