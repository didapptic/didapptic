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

namespace Didapptic\Service\App\Update;

use DateTime;
use Didapptic\Object\App;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Service\DataRequest\AppMonstaAppRequest;
use Didapptic\Service\DataRequest\iTunesAppRequest;

/**
 * Class UpdateAppService
 *
 * @package Didapptic\Service\App\Update
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class UpdateAppService {

    /** @var AppRepository */
    private $appManager;
    /** @var iTunesAppRequest */
    private $iTunesAppRequest;
    /** @var AppMonstaAppRequest */
    private $appMonstaRequest;

    public function __construct(
        AppRepository $appManager
        , iTunesAppRequest $iTunesAppRequest
        , AppMonstaAppRequest $appMonstaAppRequest
    ) {
        $this->appManager       = $appManager;
        $this->iTunesAppRequest = $iTunesAppRequest;
        $this->appMonstaRequest = $appMonstaAppRequest;
    }

    /**
     * @param string|null $storeId
     * @param array       $newParameters
     * @param bool        $request
     *
     * @return App|null
     */
    public function updateApp(
        ?string $storeId
        , array $newParameters
        , bool $request = false
    ): ?App {
        if (null === $storeId || "" === $storeId) return null;
        $app = $this->appManager->getAppByStoreId($storeId);
        if (null === $app) return null;

        $newApp = clone $app;

        if (true === $request) {
            $newApp = $this->request($newApp);
        }

        $newApp->setId($app->getId());
        $newApp->setLastUserUpdateTs($app->getLastUserUpdateTs());
        $newApp->setRecommendation($app->getRecommendation());
        $newApp->setIosPrivacy($app->getIosPrivacy());
        $newApp->setCreateTs($app->getCreateTs());
        $newApp->setUsage((float) $newParameters["usage"]);
        $newApp->setResultsQuality((float) $newParameters["results-quality"]);
        $newApp->setPresentability((float) $newParameters["presentability"]);
        $newApp->setDidacticComment($newParameters["didactic-comment"]);
        $newApp->setDidacticRemark($newParameters["didactic-remark"]);
        $newApp->setPrivacy((int) $newParameters["privacy"]);
        $newApp->setPrivacyComment($newParameters["privacy-comment"]);
        $newApp->setSubjects($newParameters["subjects"]);
        $newApp->setCategories($newParameters["categories"]);
        $newApp->setTags($newParameters["tags"]);
        $newApp->setAuthor((int) $newParameters["author"]);
        $newApp->setRecommendation((int) $newParameters["recommendation"]);
        $newApp->setLastUpdated(new DateTime());

        $this->appManager->update($newApp);
        return $newApp;
    }

    private function request(App $app): App {
        $request = $this->iTunesAppRequest;

        if ($app->isAndroid()) {
            $request = $this->appMonstaRequest;
        }

        $request->addAppId($app->getStoreId());
        $request->requestData();
        $list = $request->getData();

        if ($list->length() === 1) {
            /** @var App $app */
            $app = $list->get(0);
        }

        $this->appManager->markAppsAsDeleted(
            $request->getInactiveApps()
        );

        $request->clear();

        return $app;
    }

    public function getStoreIdsFromRequest(array $parsedBody): array {
        $match  = [];
        $result = ["google-store-id" => null, "ios-store-id" => null];

        $googleStoreUrl = isset($parsedBody["google-store-url"]) ? $parsedBody["google-store-url"] : "";
        $iosStoreUrl    = isset($parsedBody["ios-store-url"]) ? $parsedBody["ios-store-url"] : "";

        $parts = parse_url($googleStoreUrl);
        $q_    = isset($parts["query"]) ? $parts["query"] : "";

        parse_str($q_, $query);
        if (isset($query["id"]) && "" !== $query["id"]) {
            $result["google-store-id"] = $query["id"];
        }
        preg_match("/id(\d+)/", $iosStoreUrl, $match);
        if (isset($match[1]) && "" !== $match[1]) {
            $result["ios-store-id"] = $match[1];
        }
        return $result;
    }


}
