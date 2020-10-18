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

namespace Didapptic\BackgroundJob\Task;

use Didapptic\Didapptic;
use Didapptic\Object\App;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Service\App\Update\UpdateAppService;
use doganoo\Backgrounder\Task\Task;
use doganoo\PHPUtil\Log\FileLogger;

/**
 * Class UpdateAndroid
 *
 * @package Didapptic\BackgroundJob\Task
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class UpdateAndroid extends Task {

    private const MAX_NUMBER_OF_APPS = 100;

    /** @var AppRepository */
    private $appManager;
    /** @var UpdateAppService */
    private $updateAppService;

    public function __construct(
        AppRepository $appManager
        , UpdateAppService $updateAppService
    ) {
        $this->appManager       = $appManager;
        $this->updateAppService = $updateAppService;
    }

    protected function onAction(): void {
        // silence is golden
    }

    protected function action(): bool {
        FileLogger::info("start updating all google apps");

        $apps = Didapptic::getServer()->getAppsFromCache();
        $apps = $apps->subList(0, UpdateAndroid::MAX_NUMBER_OF_APPS);

        /** @var App $app */
        foreach ($apps as $app) {

            if (false === $app->isAndroid()) {
                continue;
            }

            $hasUpdates = $this->appManager->hasUpdates($app)
                || $this->isDebug();

            if (false === $hasUpdates) {
                FileLogger::debug("no updates available");
                continue;
            }

            $this->updateAppService->updateApp(
                $app->getStoreId()
                , [
                    "usage"              => $app->getUsage()
                    , "results-quality"  => $app->getResultsQuality()
                    , "presentability"   => $app->getPresentability()
                    , "didactic-comment" => $app->getDidacticComment()
                    , "didactic-remark"  => $app->getDidacticRemark()
                    , "privacy"          => $app->getPrivacy()
                    , "privacy-comment"  => $app->getPrivacyComment()
                    , "subjects"         => $app->getSubjects()
                    , "categories"       => $app->getCategories()
                    , "tags"             => $app->getTags()
                    , "author"           => $app->getAuthor()
                    , "recommendation"   => $app->getRecommendation()
                ]
                , true
            );

        }
        FileLogger::info("end updating all google apps");
        return true;
    }


    protected function onClose(): void {

    }

}
