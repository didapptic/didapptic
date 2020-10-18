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

use Didapptic\Repository\MaterialRepository;
use Didapptic\Service\Material\MaterialService;

/**
 * Class DeleteMaterialSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class DeleteMaterialSubmit extends AbstractSubmit {

    /** @var MaterialService */
    private $materialService;
    /** @var MaterialRepository */
    private $materialManager;
    /** @var int */
    private $materialId;

    public function __construct(
        MaterialService $materialService
        , MaterialRepository $materialManager
    ) {
        parent::__construct();
        $this->materialService = $materialService;
        $this->materialManager = $materialManager;
    }

    protected function valid(): bool {
        $this->materialId = (int) $this->getArgument("id");
        return null !== $this->materialId &&
            $this->materialId > 0;
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {
        $material = $this->materialManager->get($this->materialId);

        // there is no material to delete
        // we respond with true in order to
        // avoid confusing about "could not
        // delete"
        if (null === $material) return true;
        $removed = $this->materialService->remove($material);
        if (false === $removed) return false;
        return true;
    }

    protected function onDestroy(): void {

    }

}

