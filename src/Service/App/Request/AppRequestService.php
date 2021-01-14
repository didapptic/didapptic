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

namespace Didapptic\Service\App\Request;

use DateTime;
use Didapptic\Object\App;
use Didapptic\Object\AppleApp;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;
use Exception;

/**
 * Class AppRequestService
 *
 * @package Didapptic\Service\App\Request
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AppRequestService {

    public function toAppleApp(array $array): ?AppleApp {
        $app     = new AppleApp();
        $results = $array["results"];
        if (count($results) === 0) return null;
        foreach ($results as $result) {
            $supportedDevices          = $this->readArrayField($result, "supportedDevices");
            $advisories                = $this->readArrayField($result, "advisories");
            $sellerUrl                 = $this->readStringArrayField($result, "sellerUrl");
            $currentVersionReleaseDate = $this->readStringArrayField($result, "currentVersionReleaseDate");

            try {
                $x                         = new DateTime($currentVersionReleaseDate);
                $currentVersionReleaseDate = $x->getTimestamp();
            } catch (Exception $exception) {
                FileLogger::info("could not convert datetime to Object. " . ($exception->getMessage()));
                $currentVersionReleaseDate = 0;
            }

            $releaseDate       = $this->readStringArrayField($result, "releaseDate");
            $currency          = $this->readStringArrayField($result, "currency");
            $version           = $this->readStringArrayField($result, "version");
            $artistName        = $this->readStringArrayField($result, "artistName");
            $genres            = $this->readArrayField($result, "genres");
            $price             = $this->readArrayFloatField($result, "price");
            $description       = $this->readStringArrayField($result, "description");
            $bundleId          = $this->readStringArrayField($result, "bundleId");
            $minimumOsVersion  = $this->readStringArrayField($result, "minimumOsVersion");
            $primaryGenreName  = $this->readStringArrayField($result, "primaryGenreName");
            $trackId           = $this->readStringArrayField($result, "trackId");
            $trackName         = $this->readStringArrayField($result, "trackName");
            $averageUserRating = $this->readArrayFloatField($result, "averageUserRating");
            $userRatingCount   = $this->readArrayFloatField($result, "userRatingCount");
            $storeUrl          = $this->readStringArrayField($result, "trackViewUrl");

            if ($this->isEmptyString($trackId)) {
                FileLogger::info("app has no id. aborting");
                return null;
            }
            if ($this->isEmptyString($trackName)) {
                FileLogger::info("app $trackId has no name. aborting");
                return null;
            }
            if ($this->isEmptyString($artistName)) {
                FileLogger::info("app $trackId has developer. aborting");
                return null;
            }
            //if (AppFactory::isEmptyString($sellerUrl)) {
            //    FileLogger::info("app $trackId has no developer website. aborting");
            //    return null;
            //}
            if ($this->isEmptyString($description)) {
                FileLogger::info("app $trackId has no description. aborting");
                return null;
            }
            if ($this->isEmptyString($storeUrl)) {
                FileLogger::info("app $trackId has no store url. aborting");
                return null;
            }
            $app->setStoreId($trackId);
            $app->setBundleId($bundleId);
            $app->setPrice($price);
            $app->setRating($averageUserRating);
            $app->setDescription($description);
            $app->setVersion($version);
            $app->setMinimumOsVersion($minimumOsVersion);
            $dateTime = new DateTime();
            $dateTime->setTimestamp($currentVersionReleaseDate);
            $app->setLastUpdated($dateTime);
            $app->setName($trackName);
            $app->setDeveloper($artistName);
            $app->setSupportedDevices($supportedDevices);
            $app->setPriceCurrency($currency);
            /** @phpstan-ignore-next-line */
            $app->setGenres($genres);
            $app->setDeveloperWebsite($sellerUrl);
            $dateTime = null;

            if (null !== $releaseDate) {
                $dateTime = new DateTime();
                $dateTime->setTimestamp((int) $releaseDate);
            }

            $app->setReleaseDate($dateTime);
            $app->setOperatingSystem(App::IOS);
            $app->setStoreURL($storeUrl);
            $app->setResultsQuality((float) 0);
            $app = $this->fromIOS($app, $result);
        }
        /** @phpstan-ignore-next-line */
        return $app;
    }

    private function readArrayField(array $array, string $name): array {
        if (isset($array[$name])) {
            if (is_array($array[$name])) {
                return $array[$name];
            }
        }
        return [];
    }

    private function readStringArrayField(array $array, string $name): string {
        if (isset($array[$name])) {
            if (false === $this->isEmptyString($array[$name])) {
                return (string) $array[$name];
            }
        }
        return "";
    }

    private function isEmptyString(?string $value): bool {
        return null === $value || trim((string) $value) === "";
    }

    public function readArrayFloatField(array $array, ?string $name): float {
        if (isset($array[$name])) {
            if (false === $this->isEmptyString($array[$name])) {
                return floatval($array[$name]);
            }
        }
        return 0.0;
    }

    private function fromIOS(App $app, array $array): App {
        $iconURL            = $this->readStringArrayField($array, "artworkUrl512");
        $screenshotUrls     = $this->readArrayField($array, "screenshotUrls");
        $ipadScreenshotUrls = $this->readArrayField($array, "ipadScreenshotUrls");
        $arr                = [
            "icon"               => json_encode($iconURL)
            , "screenshots"      => json_encode($screenshotUrls)
            , "ipad_screenshots" => json_encode($ipadScreenshotUrls)

        ];
        $app->setUrls($arr);
        return $app;
    }

    public function getValidatedList(ArrayList $list): ArrayList {
        /** @var App $app */
        foreach ($list as $key => $app) {
            $valid = $this->validate($app);
            if (!$valid) {
                $list->remove($key);
                FileLogger::info("{$app->getStoreId()} is not valid. Removed");
            }
        }
        return $list;
    }

    public function validate(App $app): bool {
        $usage = $app->getUsage();
        if (!is_float($usage)) return false;
        $resultsQuality = $app->getResultsQuality();
        $resultsQuality = floatval($resultsQuality);
        if (!is_float($resultsQuality) || $resultsQuality < 0 || $resultsQuality > 5) return false;
        FileLogger::debug("passed results quality");
        $presentability = $app->getPresentability();
        $presentability = floatval($presentability);
        if (!is_float($presentability) || $presentability < 0 || $presentability > 5) return false;
        FileLogger::debug("passed presentable");
        $didacticComment = $app->getDidacticComment();
        if (!is_string($didacticComment) || strlen($didacticComment) === 0) return false;
        FileLogger::debug("passed didactic comment");
        $didacticRemark = $app->getDidacticRemark();
        if (!is_string($didacticRemark) || strlen($didacticRemark) === 0) return false;
        FileLogger::debug("passed didactic remark");
        $privacy = $app->getPrivacy();
        $privacy = intval($privacy);
        if (!is_int($privacy) || $privacy < 0 || $privacy > 4) return false;
        FileLogger::debug("passed privacy");
        $subjects = $app->getSubjects();
        if (!is_array($subjects) || count($subjects) < 1) return false;
        FileLogger::debug("passed subjects");
        $categories = $app->getCategories();
        if (!is_array($categories) || count($categories) < 1) return false;
        FileLogger::debug("passed categories");
        $tags = $app->getTags();
        if (!is_array($tags) || count($tags) < 1) return false;
        FileLogger::debug("passed tags");
        $author = $app->getAuthor();
        if ($author < 1) return false;
        FileLogger::debug("passed authr");
        $recommendation = $app->getRecommendation();
        $recommendation = intval($recommendation);
        if (!is_int($recommendation) || $recommendation < 0 || $recommendation > 3) return false;
        FileLogger::debug("passed recommendation");
        return true;
    }

    public function toUserApp(ArrayList $list, array $array): ArrayList {
        if ($list->length() === 0) return $list;
        $app = $list->get(0);
        $app = $this->arrayToApp($array, $app);
        $list->set(0, $app);
        return $list;
    }

    public function arrayToApp(array $array, ?App $app = null): App {
        if (null === $app) $app = new App();

        $operatingSystem = $this->readIntegerArrayField($array, "operating-system");
        $author          = $this->readIntegerArrayField($array, "author");
        $usage           = $this->readArrayFloatField($array, "usage");
        $resultsQuality  = $this->readArrayFloatField($array, "results-quality");
        $presentability  = $this->readArrayFloatField($array, "presentability");
        $didacticComment = $this->readStringArrayField($array, "didactic-comment");
        $didacticRemark  = $this->readStringArrayField($array, "didactic-remark");
        $privacyComment  = $this->readStringArrayField($array, "privacy-comment");
        $privacy         = $this->readStringArrayField($array, "privacy");
        $recommendation  = $this->readStringArrayField($array, "recommendation");
        $subjects        = $this->readArrayField($array, "subjects");
        $categories      = $this->readArrayField($array, "categories");
        $tags            = $this->readArrayField($array, "tags");
        $iosPrivacy      = $this->readStringArrayField($array, "ios-privacy");

        $app->setOperatingSystem((int) $operatingSystem);
        $app->setUsage((float) $usage);
        $app->setResultsQuality((float) $resultsQuality);
        $app->setPresentability((float) $presentability);
        $app->setDidacticComment((string) $didacticComment);
        $app->setDidacticRemark((string) $didacticRemark);
        $app->setPrivacy((int) $privacy);
        $app->setPrivacyComment((string) $privacyComment);
        $app->setAuthor((int) $author);
        $app->setRecommendation((int) $recommendation);
        $app->setSubjects((array) $subjects);
        $app->setCategories((array) $categories);
        $app->setTags((array) $tags);
        $app->setIosPrivacy((string) $iosPrivacy);
        $app->setLastUserUpdateTs(new DateTime());
        return $app;
    }

    private function readIntegerArrayField(array $array, string $name): int {
        return (int) $array[$name] ?? 0;
    }

}
