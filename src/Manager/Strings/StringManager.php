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

namespace Didapptic\Manager\Strings;

use Didapptic\Object\Constant\Extension;
use DirectoryIterator;
use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;

/**
 * Class StringManager
 *
 * @package Didapptic\Manager\Strings
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class StringManager {

    /** @var array */
    private $paths = [];

    public function addPath(string $path): void {
        $this->paths[] = $path;
    }

    public function getAll(): HashMap {
        $table = new HashMap();

        foreach ($this->paths as $path) {
            $dir = new DirectoryIterator($path);

            foreach ($dir as $fileInfo) {
                if (true === $fileInfo->isDot()) continue;
                if (true === $fileInfo->isDir()) continue;

                if (Extension::JSON === $fileInfo->getExtension()) {
                    $content = file_get_contents((string) $fileInfo->getRealPath());
                    $content = json_decode((string) $content, true);

                    if (null !== $content) {
                        $table->put($fileInfo->getBasename(), $content);
                    }
                }
            }
        }
        return $table;
    }

}
