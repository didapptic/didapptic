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

use Didapptic\Service\App\Update\UpdateAppService;
use Didapptic\Service\AppService;

/**
 * Class UpdateAppSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class UpdateAppSubmit extends AbstractSubmit {

    /** @var UpdateAppService */
    private $updateAppService;

    public function __construct(UpdateAppService $updateAppService) {
        parent::__construct();
        $this->updateAppService = $updateAppService;
    }

    protected function valid(): bool {
        return true;
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {
        $arguments = $this->getArguments();
        $storeIds  = $this->updateAppService->getStoreIdsFromRequest($arguments);

        if (count($storeIds) === 0) {
            return false;
        }

        $this->updateAppService->updateApp(
            $storeIds["ios-store-id"] ?? null
            , $arguments
            , false
        );
        $this->updateAppService->updateApp(
            $storeIds["google-store-id"] ?? null
            , $arguments
            , false
        );

        return true;
    }

    protected function onDestroy(): void {

    }

}
