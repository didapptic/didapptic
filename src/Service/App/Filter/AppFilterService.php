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

namespace Didapptic\Service\App\Filter;

use Didapptic\Service\App\Filter\Applier\Category;
use Didapptic\Service\App\Filter\Applier\IFilterApplier;
use Didapptic\Service\App\Filter\Applier\OperatingSystem;
use Didapptic\Service\App\Filter\Applier\Subject;
use Didapptic\Service\App\Filter\Applier\Text;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;

class AppFilterService {


    public function filterApps(array $arguments, ArrayList $apps): ArrayList {

        $filters = [
            IFilterApplier::TYPE_OPERATING_SYSTEM =>
                new OperatingSystem(
                    $arguments[IFilterApplier::TYPE_OPERATING_SYSTEM] ?? []
                )
            , IFilterApplier::TYPE_SUBJECT        =>
                new Subject(
                    $arguments[IFilterApplier::TYPE_SUBJECT] ?? []
                )
            , IFilterApplier::TYPE_CATEGORY       =>
                new Category(
                    $arguments[IFilterApplier::TYPE_CATEGORY] ?? []
                )
            , IFilterApplier::TYPE_TEXT           =>
                new Text(
                    [$arguments[IFilterApplier::TYPE_TEXT]] ?? []
                )
        ];

        /**
         * @var string         $name
         * @var IFilterApplier $filter
         */
        foreach ($filters as $name => $filter) {
            FileLogger::debug("current filter: $name");
            FileLogger::debug("size pre filter: ". $apps->length());
            $apps = $filter->filter($apps);
            FileLogger::debug("size post filter: ". $apps->length());
        }

        return $apps;
    }

}
