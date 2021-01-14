<?php
declare(strict_types=1);

namespace Didapptic\Factory;

use Didapptic\Object\App;
use function is_array;
use function json_encode;
use function trim;

/**
 * Class URLFactory
 *
 * @package Didapptic\Factory
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 * @deprecated
 */
class URLFactory {

    private function __construct() {

    }

    public static function fromIOS(App $app, array $array): App {
        $iconURL            = URLFactory::readStringArrayField($array, "artworkUrl512");
        $screenshotUrls     = URLFactory::readArrayField($array, "screenshotUrls");
        $ipadScreenshotUrls = URLFactory::readArrayField($array, "ipadScreenshotUrls");
        $arr                = [
            "icon"               => json_encode($iconURL)
            , "screenshots"      => json_encode($screenshotUrls)
            , "ipad_screenshots" => json_encode($ipadScreenshotUrls)

        ];
        $app->setUrls($arr);
        return $app;
    }

    private static function readStringArrayField(array $array, string $name): string {
        return URLFactory::isEmptyString($array[$name]) ? "" : $array[$name];
    }

    private static function isEmptyString(?string $value): bool {
        if (null == $value || trim($value) == "") {
            return true;
        }
        return false;
    }

    private static function readArrayField(array $array, string $name): array {
        return is_array($array[$name]) ? $array[$name] : [];
    }

    public static function fromGoogle(App $app, array $array): App {
        $iconURL        = URLFactory::readStringArrayField($array, "icon_url");
        $screenshotUrls = URLFactory::readArrayField($array, "screenshot_urls");

        $arr = [
            "icon"        => json_encode($iconURL),
            "screenshots" => json_encode($screenshotUrls),
        ];
        $app->setUrls($arr);
        return $app;
    }

}
