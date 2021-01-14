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

namespace Didapptic\Object;

use DateTime;
use Didapptic\Factory\URLFactory;
use doganoo\PHPUtil\Log\FileLogger;
use Exception;
use function count;
use function floatval;
use function intval;
use function is_array;
use function is_float;
use function is_int;
use function is_string;
use function strlen;
use function trim;

/**
 * Class AppFactory
 *
 * @package    Didapptic\Object
 * @author     Dogan Ucar <dogan@dogan-ucar.de>
 * @deprecated migrate everything to src/Service/App/Submit/AppSubmitService.php or
 *             src/Service/App/Update/UpdateAppService.php
 */
class AppFactory {

    public static function toAndroidApp(?array $array): ?App {
        if (null === $array) return null;
        $storeId          = $array["id"];
        $name             = $array["app_name"];
        $minimumOsVersion = AppFactory::readStringArrayField($array, "requires_os");
        $price            = AppFactory::readArrayFloatField($array, "price");
        $developer        = $array["publisher_name"];
        $version          = AppFactory::readStringArrayField($array, "version");
        $developerWebsite = $array["publisher_url"];
        $description      = $array["description"];
        $permissions      = AppFactory::readArrayField($array, "permissions");
        $lastUpdated      = (int) AppFactory::readIntegerArrayField($array, "status_unix_timestamp");
        $rating           = AppFactory::readArrayFloatField($array, "all_rating");
        $related          = AppFactory::readArrayField($array, "related");
        $priceCurreny     = AppFactory::readStringArrayField($array, "price_currency");
        $genres           = AppFactory::readArrayField($array, "genres");
        $developerId      = $array["publisher_id"];
        $developerEmail   = AppFactory::readStringArrayField($array, "publisher_email");
        $downloads        = AppFactory::readStringArrayField($array, "downloads");
        $genre            = AppFactory::readStringArrayField($array, "genre");
        $bundleId         = AppFactory::readStringArrayField($array, "bundle_id");
        $releaseDate      = AppFactory::readStringArrayField($array, "release_date");
        $storeURL         = AppFactory::readStringArrayField($array, "store_url");


        if (AppFactory::isEmptyString($storeId)) {
            FileLogger::info("app has no id. aborting");
            return null;
        }
        if (AppFactory::isEmptyString($name)) {
            FileLogger::info("app $storeId has no name. aborting");
            return null;
        }
        if (AppFactory::isEmptyString($developer)) {
            FileLogger::info("app $storeId has no developer. aborting");
            return null;
        }
        //if (AppFactory::isEmptyString($developerWebsite)) {
        //    FileLogger::info("app $storeId has no developer website. aborting");
        //    return null;
        //}
        if (AppFactory::isEmptyString($description)) {
            FileLogger::info("app $storeId has no description. aborting");
            return null;
        }
        if (AppFactory::isEmptyString($developerId)) {
            FileLogger::info("app $storeId has no developer id. aborting");
            return null;
        }
        if (AppFactory::isEmptyString($storeURL)) {
            FileLogger::info("app $storeId has no store url. aborting");
            return null;
        }

        $app = new AndroidApp();
        $app->setStoreId($storeId);
        $app->setName($name);
        $app->setMinimumOsVersion($minimumOsVersion);
        $app->setPrice($price);
        $app->setDeveloper($developer);
        $app->setVersion($version);
        $app->setDeveloperWebsite($developerWebsite);
        $app->setDescription($description);
        $app->setPermissions($permissions);
        $dateTime = new DateTime();
        $dateTime->setTimestamp($lastUpdated);
        $app->setLastUpdated($dateTime);
        $app->setRating($rating);
        $app->setRelated($related);
        $app->setPriceCurrency($priceCurreny);
//        $app->setGenres($genres);
        $app->setPublisherId($developerId);
        $app->setDeveloperEmail($developerEmail);
        $app->setSupportedDevices([]);
        // There is a sometimes a "10,000+" string in this
        // field. Fix this when using!
        // $app->setNumberOfDownloads($downloads);
        $app->setBundleId($bundleId);
        /** @phpstan-ignore-next-line */
        $app->setReleaseDate(DateTime::createFromFormat("Y-m-d", $releaseDate));
        $app->setOperatingSystem(App::ANDROID);
        $app->setStoreURL($storeURL);
        $app = URLFactory::fromGoogle($app, $array);
        return $app;
    }

    private static function readStringArrayField(array $array, string $name): string {
        if (isset($array[$name])) {
            if (!AppFactory::isEmptyString($array[$name])) {
                return (string) $array[$name];
            }
        }
        return "";
    }

    /** @phpstan-ignore-next-line */
    private static function isEmptyString($value): bool {
        if (null === $value || trim((string) $value) === "") {
            return true;
        }
        return false;
    }

    public static function readArrayFloatField(array $array, string $name): float {
        if (isset($array[$name])) {
            if (!AppFactory::isEmptyString($array[$name])) {
                return floatval($array[$name]);
            }
        }
        return 0.0;
    }

    private static function readArrayField(array $array, string $name): array {
        if (isset($array[$name])) {
            if (is_array($array[$name])) {
                return $array[$name];
            }
        }
        return [];
    }

    private static function readIntegerArrayField(array $array, string $name): int {
        return (int) $array[$name] ?? 0;
    }

    public static function toAppleApp(array $array): ?AppleApp {
        $app     = new AppleApp();
        $results = $array["results"];
        if (count($results) === 0) return null;
        foreach ($results as $result) {
            $supportedDevices          = AppFactory::readArrayField($result, "supportedDevices");
            $advisories                = AppFactory::readArrayField($result, "advisories");
            $sellerUrl                 = AppFactory::readStringArrayField($result, "sellerUrl");
            $currentVersionReleaseDate = AppFactory::readStringArrayField($result, "currentVersionReleaseDate");
            try {
                $x                         = new DateTime($currentVersionReleaseDate);
                $currentVersionReleaseDate = $x->getTimestamp();
            } catch (Exception $exception) {
                FileLogger::info("could not convert datetime to Object. " . ($exception->getMessage()));
                $currentVersionReleaseDate = 0;
            }
            $releaseDate       = AppFactory::readStringArrayField($result, "releaseDate");
            $currency          = AppFactory::readStringArrayField($result, "currency");
            $version           = AppFactory::readStringArrayField($result, "version");
            $artistName        = AppFactory::readStringArrayField($result, "artistName");
            $genres            = AppFactory::readArrayField($result, "genres");
            $price             = AppFactory::readArrayFloatField($result, "price");
            $description       = AppFactory::readStringArrayField($result, "description");
            $bundleId          = AppFactory::readStringArrayField($result, "bundleId");
            $minimumOsVersion  = AppFactory::readStringArrayField($result, "minimumOsVersion");
            $primaryGenreName  = AppFactory::readStringArrayField($result, "primaryGenreName");
            $trackId           = AppFactory::readStringArrayField($result, "trackId");
            $trackName         = AppFactory::readStringArrayField($result, "trackName");
            $averageUserRating = AppFactory::readArrayFloatField($result, "averageUserRating");
            $userRatingCount   = AppFactory::readArrayFloatField($result, "userRatingCount");
            $storeUrl          = AppFactory::readStringArrayField($result, "trackViewUrl");

            if (AppFactory::isEmptyString($trackId)) {
                FileLogger::info("app has no id. aborting");
                return null;
            }
            if (AppFactory::isEmptyString($trackName)) {
                FileLogger::info("app $trackId has no name. aborting");
                return null;
            }
            if (AppFactory::isEmptyString($artistName)) {
                FileLogger::info("app $trackId has developer. aborting");
                return null;
            }
            //if (AppFactory::isEmptyString($sellerUrl)) {
            //    FileLogger::info("app $trackId has no developer website. aborting");
            //    return null;
            //}
            if (AppFactory::isEmptyString($description)) {
                FileLogger::info("app $trackId has no description. aborting");
                return null;
            }
            if (AppFactory::isEmptyString($storeUrl)) {
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
            $app = URLFactory::fromIOS($app, $result);
        }
        /** @phpstan-ignore-next-line */
        return $app;
    }

    public static function validate(App $app): bool {
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

}
