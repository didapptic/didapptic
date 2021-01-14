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

use Curl\Curl;
use Didapptic\Didapptic;
use Didapptic\Object\AppFactory;
use Didapptic\Object\AppResponse;
use Didapptic\Object\Environment;
use Didapptic\Repository\OptionRepository;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;
use function file_put_contents;
use function gzdecode;
use function intval;
use function is_file;
use function json_decode;
use function sleep;
use function unlink;
use function utf8_encode;

/**
 * Class AppMonstaAppRequest
 *
 * @package Didapptic\Service\DataRequest
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AppMonstaAppRequest extends AbstractAppRequest {

    /** @var OptionRepository */
    private $optionManager;
    /** @var Environment */
    private $environment;

    public function __construct(
        OptionRepository $optionManager
        , Environment $environment
    ) {
        parent::__construct();
        $this->optionManager = $optionManager;
        $this->environment   = $environment;
    }

    public function requestData(): AppResponse {
        $option       = $this->optionManager->getOption("com.appmonsta.api.requestcount", "0");
        $sleepSeconds = $this->environment->read("system.settings.appmonsta.request.seconds.sleep");
        $savePath     = Didapptic::getServer()->getAppCachePath() . "/android";
        $useCachedApp = $this->environment->isDebug();
        $limit        = $this->environment->read("system.settings.appmonsta.request.limit");
        $appRequest   = $option->getValue();
        $appRequest   = intval($appRequest);
        $response     = new AppResponse();
        $i            = 0;

        foreach ($this->getAppList() as $appId) {
            if ($appRequest >= $limit) {
                $message = "limit reached: $appRequest";
                $response->setStatus(AppResponse::LIMIT_REACHED);
                $response->setMessage("limit reached: $appRequest, number of imported apps: $i");
                FileLogger::info($message);
                return $response;
            }
            $i++;
            $fileName = "$savePath/{$appId}.json";
            if (is_file($fileName) && $useCachedApp) {
                FileLogger::info("using cached app for $appId");
                $string = file_get_contents($fileName);
                $string = utf8_encode((string) $string);
                $json   = json_decode($string, true);
                $app    = AppFactory::toAndroidApp($json);
                $this->addApp($app);
                continue;
            }
            FileLogger::info("no cached app for $appId. Requesting from appmonsta");
            /** @var Curl $curl */
            $curl = new Curl(); //TODO use guzzle http instead
            $curl->setHeader("Accept-Encoding", "deflate, gzip");
            $appMonstaApiKey = $this->environment->read("system.settings.appmonsta.apikey");
            $curl->setBasicAuthentication($appMonstaApiKey
                , 'X');
            $curl->get("https://api.appmonsta.com/v1/stores/android/details/$appId.json",
                [
                    "country" => "US",
                ]

            );

            if ($curl->error) {
                FileLogger::error("curl request for id $appId returned the following error: [{$curl->errorCode} => {$curl->errorMessage}] ");
                $added = $this->addInactiveApp($appId);
                FileLogger::debug("added $added");
                continue;
            }

            $string = gzdecode($curl->response);
            $string = utf8_encode((string) $string);
            $json   = json_decode($string, true);
            $app    = AppFactory::toAndroidApp($json);

            if (null === $app) {
                FileLogger::error('no app !!!!!!');
                continue;
            }
            $fileName = "$savePath/{$app->getStoreId()}.json";
            if (is_file($fileName)) {
                FileLogger::info("is file. going to delete");
                unlink($fileName);
            }
            file_put_contents($fileName, $string);
            $this->addApp($app);
            sleep((int) $sleepSeconds);

            $appRequest++;
        }

        FileLogger::debug("ending");
        FileLogger::debug($this->getInactiveApps()->length());
        $this->optionManager->addOption("com.appmonsta.api.requestcount", (string) $appRequest);
        $response->setStatus(0);
        $response->setMessage("number of imported apps: $i");
        return $response;
    }

    public function getAppList(): ArrayList {
        return parent::getAppList();
    }

    public function clear(): void {
        parent::clear();
    }

}
