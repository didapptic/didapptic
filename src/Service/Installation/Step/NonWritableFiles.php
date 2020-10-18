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

use Didapptic\Service\Installation\Files\FileService;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;

/**
 * Class NonWritableFiles
 *
 * @package Didapptic\Service\Installation\Step
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class NonWritableFiles implements IStep {

    /** @var FileService */
    private $fileService;

    /**
     * NonWritableFiles constructor.
     *
     * @param FileService $fileService
     */
    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
    }

    /**
     * returns a boolean that indicates whether all necessary files
     * are writable on the system
     *
     * @return bool
     */
    public function isValid(): bool {
        $files = $this->getNonWritableFiles();
        if ($files->length() > 0) {
            FileLogger::debug("not all files are writable. The following files are not writable: " . (json_encode($files)));
            return false;
        }
        return true;
    }

    /**
     * returns a list of files that are necessary to run the app
     * but missing on the system
     *
     * @return ArrayList
     */
    private function getNonWritableFiles(): ArrayList {
        $files = $this->fileService->getNecessaryFiles();
        foreach ($files as $key => $file) {
            if (is_writable($file)) {
                $files->remove($key);
            }
        }
        return $files;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return NonWritableFiles::class;
    }

}
