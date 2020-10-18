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

namespace Didapptic\Service\Installation\Step;

use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\System\Properties;

/**
 * Class PropertyFile
 *
 * @package Didapptic\Service\Installation\Step
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class PropertyFile implements IStep {

    /** @var Properties */
    private $sysProperties;
    /** @var Properties */
    private $sampleProperties;

    /**
     * PropertyFile constructor.
     *
     * @param Properties $sysProperties
     * @param Properties $sampleProperties
     */
    public function __construct(
        Properties $sysProperties
        , Properties $sampleProperties
    ) {
        $this->sysProperties    = $sysProperties;
        $this->sampleProperties = $sampleProperties;
    }

    /**
     * returns a boolean that indicates whether all properties
     * are presentable in order to run the system
     *
     * @return bool
     */
    public function isValid(): bool {
        $difference       = $this->getPropertyDifference();
        $hasAllProperties = count($difference) === 0;
        if (!$hasAllProperties) {
            FileLogger::debug("not all properties are set. The following properties are missing: " . (json_encode($difference)));
            return false;
        }
        return true;
    }

    /**
     * returns the difference between the sample and
     * real properties file
     *
     * @return array
     */
    private function getPropertyDifference(): array {
        $sample     = $this->sampleProperties->keySet();
        $properties = $this->sysProperties->keySet();
        return array_diff($sample, $properties);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return PropertyFile::class;
    }

}
