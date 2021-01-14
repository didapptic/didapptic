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

namespace Didapptic\Service\App\Submit;

use DateTime;
use Didapptic\Object\App;
use Didapptic\Object\AppResponse;
use Didapptic\Object\Environment;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Service\App\Request\AppRequestService;
use Didapptic\Service\DataRequest\AppMonstaAppRequest;
use Didapptic\Service\DataRequest\iTunesAppRequest;
use doganoo\PHPUtil\Log\FileLogger;
use Exception;

/**
 * Class AppSubmitService
 *
 * I would not touch this class, as it is a refactoring
 * from a Factory class which has used static methods
 * and checks the most edge and corner cases returned
 * by iTunes and AppMonsta.
 *
 * @package Didapptic\Service\App\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AppSubmitService {

    /** @var AppRepository */
    private $appManager;
    /** @var Environment */
    private $environment;
    /** @var AppMonstaAppRequest */
    private $appMonstaRequest;
    /** @var iTunesAppRequest */
    private $iTunesAppRequest;
    /** @var AppRequestService */
    private $appRequestService;

    public function __construct(
        AppRepository $appManager
        , Environment $environment
        , AppMonstaAppRequest $appMonstaAppRequest
        , iTunesAppRequest $iTunesAppRequest
        , AppRequestService $appRequestService
    ) {
        $this->appManager        = $appManager;
        $this->environment       = $environment;
        $this->appMonstaRequest  = $appMonstaAppRequest;
        $this->iTunesAppRequest  = $iTunesAppRequest;
        $this->appRequestService = $appRequestService;
    }


    public function addApp(
        string $storeId
        , array $parameters
        , int $operatingSystem
    ): bool {
        $app                    = $this->appManager->getAppByStoreId($storeId, true);
        $appExists              = null !== $app;
        $appExistsButWasDeleted = true === $appExists && null !== $app->getDeleteTs();
        $debug                  = $this->environment->isDebug();

        if (true === $appExists) {
            FileLogger::info("app $storeId exists already. Please update!");
            return false;
        }

        // TODO server side parameter validation goes here !
//        /** @var HashMap $users */
//        $users = Didapptic::getServer()->query(Server::USER_HASH_MAP);
//        if (false === $users->containsKey((int) $app->getAuthor())) {
//        }
        if (0 === count($parameters)) {
            FileLogger::info("no arguments passed. Please try again!!");
            return false;
        }

        //delete before insert in debug mode
        if (true === $debug) {
            $this->appManager->deleteByStoreId($storeId);
        }

        if (true === $appExistsButWasDeleted) {
            FileLogger::debug("App already exists, but got deleted before. Reactivating and aborting!");
            /** @phpstan-ignore-next-line */
            $app->setDeleteTs(null);
            /** @phpstan-ignore-next-line */
            $app->setCreateTs(new DateTime());

            $app = $this->appRequestService->arrayToApp(
                $parameters
                , $app
            );

            return $this->appManager->update($app);
        }

        $request = $this->iTunesAppRequest;

        if (App::ANDROID === $operatingSystem) {
            $request = $this->appMonstaRequest;
        }

        $request->addAppId($storeId);
        $appResponse = null;
        try {
            $appResponse = $request->requestData();
            $appList     = $request->getData();
            $appList     = $this->appRequestService->toUserApp($appList, $parameters);
            $appList     = $this->appRequestService->getValidatedList($appList);
            $inserted    = $this->appManager->insertAll($appList);
            $appList->clear();
        } catch (Exception $e) {
            FileLogger::error($e->getTraceAsString());
            return true;
        }

        // android case:
        // due to the free plan that we are using for appMonsta
        // we are limited to get 100 app data a day
        if (
            App::ANDROID === $operatingSystem
            && null !== $appResponse
            && AppResponse::LIMIT_REACHED === $appResponse->getStatus()
        ) {
            FileLogger::info("app monsta limit reached. Could not insert app or apps");
            return false;
        }

        if (false === $inserted) {

            FileLogger::error("errors during insertion");
            return false;
        }

        return true;
    }

}
