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

namespace Didapptic\Service\DataRequest;

use Didapptic\Object\App;
use Didapptic\Object\AppResponse;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class AbstractAppRequest
 *
 * @package Didapptic\Service\DataRequest
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
abstract class AbstractAppRequest {

    /** @var ArrayList */
    private $appList;
    /** @var ArrayList */
    private $resultList;
    /** @var ArrayList */
    private $inactiveApps;

    public function __construct() {
        $this->appList      = new ArrayList();
        $this->resultList   = new ArrayList();
        $this->inactiveApps = new ArrayList();
    }

    public abstract function requestData(): AppResponse;

    public function addAppId(string $appId): bool {
        return $this->appList->add($appId);
    }

    public function getData(): ArrayList {
        return $this->resultList;
    }

    public function clear(): void {
        $this->appList->clear();
        $this->resultList->clear();
        $this->inactiveApps->clear();
    }

    public function addInactiveApp(string $id): bool {
        return $this->inactiveApps->add($id);
    }

    public function getInactiveApps(): ArrayList {
        return $this->inactiveApps;
    }

    protected function addApp(?App $app): bool {
        if (null === $app) {
            return false;
        }
        return $this->resultList->add($app);
    }

    protected function getAppList(): ArrayList {
        return $this->appList;
    }

}
