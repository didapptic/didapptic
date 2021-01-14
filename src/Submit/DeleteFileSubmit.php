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

namespace Didapptic\Submit;

use Didapptic\Object\File;
use Didapptic\Object\Material;
use Didapptic\Repository\FileRepository;
use Didapptic\Repository\MaterialRepository;
use Didapptic\Service\Installation\Files\FileService;
use Exception;

/**
 * Class DeleteFileSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class DeleteFileSubmit extends AbstractSubmit {

    /** @var FileService */
    private $fileService;
    /** @var FileRepository */
    private $fileManager;
    /** @var MaterialRepository */
    private $materialManager;
    /** @var File|null */
    private $file;
    /** @var Material|null */
    private $material;

    public function __construct(
        FileService $fileService
        , FileRepository $fileManager
        , MaterialRepository $materialManager
    ) {
        parent::__construct();
        $this->fileService     = $fileService;
        $this->fileManager     = $fileManager;
        $this->materialManager = $materialManager;
    }

    protected function valid(): bool {
        $fileId         = $this->getArgument("id");
        $materialId     = $this->getArgument("materialId");
        $this->file     = $this->fileManager->getById((int) $fileId);
        $this->material = $this->materialManager->get((int) $materialId);
        return null !== $this->file && null !== $this->material;
    }

    protected function onCreate(): void {

    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function create(): bool {
        if (null === $this->file) {
            throw new Exception('no file');
        }

        if (null === $this->material) {
            throw new Exception('no material');
        }
        return $this->fileService->remove(
            $this->file
            , $this->material
        );
    }

    protected function onDestroy(): void {

    }

}

