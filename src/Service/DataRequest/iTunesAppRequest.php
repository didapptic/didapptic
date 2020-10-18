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
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Log\Logger;
use ErrorException;
use function file_put_contents;
use function gzdecode;
use function is_file;
use function json_decode;
use function unlink;

/**
 * Class iTunesAppRequest
 *
 * @package Didapptic\Service\DataRequest
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class iTunesAppRequest extends AbstractAppRequest {

    private $optionManager = null;
    private $sysProperties = null;

    public function __construct(
        OptionRepository $optionManager
        , Environment $sysProperties
    ) {
        parent::__construct();
        $this->optionManager = $optionManager;
        $this->sysProperties = $sysProperties;
    }

    public function requestData(): AppResponse {
        $savePath     = Didapptic::getServer()->getAppCachePath() . "/ios";
        $useCachedApp = $this->sysProperties->isDebug();
        $response     = new AppResponse();
        foreach (parent::getAppList() as $appId) {
            $fileName = "$savePath/{$appId}.json";
            if (is_file($fileName) && $useCachedApp) {
                FileLogger::debug("using file");
                $string = file_get_contents($fileName);
                $json   = json_decode($string, true);
                $app    = AppFactory::toAppleApp($json);
                $this->addApp($app);
                continue;
            }
            try {
                $curl = new Curl();//TODO use guzzle http instead
                $curl->setHeader("Connection", "close");
                $curl->setHeader("Accept-Encoding", "deflate, gzip");
                $curl->get("https://itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsLookup",
                    [
                        "id"      => $appId,
                        "country" => "de",
                    ]
                );

                if ($curl->error) {
                    FileLogger::error("curl request returned the following error: [{$curl->errorCode} => {$curl->errorMessage}]");
                    $this->addInactiveApp($appId);
                    continue;
                }
                $string   = gzdecode($curl->response);
                $fileName = "$savePath/{$appId}.json";
                if (is_file($fileName)) {
                    unlink($fileName);
                }
                file_put_contents($fileName, $string);
                $json = json_decode($string, true);
                if (($json['resultCount'] ?? 0) === 0) {
                    $this->addInactiveApp($appId);
                    continue;
                }
                $app = AppFactory::toAppleApp($json);
                $this->addApp($app);
            } catch (ErrorException $e) {
                Logger::error($e->getTraceAsString());
                FileLogger::error($e->getTraceAsString());
            }

        }
        $response->setStatus(0);
        $response->setMessage("number of imported apps: " . (parent::getAppList()->length()));
        return $response;
    }

}
