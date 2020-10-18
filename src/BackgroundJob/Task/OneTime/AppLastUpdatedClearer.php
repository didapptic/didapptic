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

namespace Didapptic\BackgroundJob\Task\OneTime;

use DateTime;
use Didapptic\Didapptic;
use Didapptic\Object\App;
use Didapptic\Repository\App\AppRepository;
use doganoo\Backgrounder\Task\Task;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class AppLastUpdatedClearer
 *
 * @package Didapptic\BackgroundJob\Task\OneTime
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AppLastUpdatedClearer extends Task {

    /** @var AppRepository */
    private $appManager;
    /** @var ArrayList */
    private $apps;

    /**
     * AppLastUpdatedClearer constructor.
     *
     * @param AppRepository $appManager
     */
    public function __construct(AppRepository $appManager) {
        $this->appManager = $appManager;
        $this->apps       = new ArrayList();
    }

    protected function onAction(): void {
        $this->apps = Didapptic::getServer()->getAppsFromCache();
    }

    /**
     * @return bool
     */
    protected function action(): bool {

        /** @var App $app */
        foreach ($this->apps as &$app) {

            $app->setLastUserUpdateTs(
                $this->getDefaultDate()
            );

        }

        $this->appManager->updateAll($this->apps);
        return true;
    }

    /**
     * @return DateTime
     */
    private function getDefaultDate(): DateTime {
        $now = new DateTime();
        $now->modify("-10 year");
        return $now;
    }

    protected function onClose(): void {

    }

}
