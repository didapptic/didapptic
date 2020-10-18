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

namespace Didapptic;

use Didapptic\Object\Application\Supporter\Supporter;
use Didapptic\Service\Installation\Installer;
use Didapptic\Service\Session\SessionHandler;
use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;

/**
 * Class Didapptic
 *
 * @package Didapptic
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Didapptic {

    public const APP_NAME    = "didapptic";
    public const APP_USER_ID = 9999;

    /** @var Server */
    private static $server;

    /**
     * Didapptic constructor.
     */
    public function __construct() {
        Didapptic::$server = new Server(
            Didapptic::getAppRoot()
        );
    }

    public static function getAppRoot(): string {
        return str_replace("\\", '/', substr(__DIR__, 0, -3));
    }

    public static function getBaseURL(bool $withScript = true): ?string {
        $scriptName          = "index.php";
        $scriptNameToReplace = $scriptName;

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

        $position = strpos($url, $scriptName);
        $position = false === $position ? 0 : $position;

        if ($withScript) {
            return substr($url, 0, $position) . $scriptNameToReplace;
        } else {
            return substr($url, 0, $position) . "";
        }
    }

    /**
     * @return HashMap
     */
    public static function getSupporters(): HashMap {
        $hashMap = new HashMap();
        $hashMap->put(
            "main"
            , new Supporter(
            "Schwerpunkt Kunstdidaktik des Instituts für Kunstpädagogik der Goethe-Universität Frankfurt am Main"
            , "https://www.uni-frankfurt.de/44259890/Fachdidaktik"
            , "Unterstützt durch den Schwerpunkt Kunstdidaktik des Instituts für Kunstpädagogik der Goethe-Universität Frankfurt am Main"
        ));
        $hashMap->put(
            "dev"
            , new Supporter(
                "Doğan Uçar"
                , "https://www.dogan-ucar.de"
            )
        );
        $hashMap->put(
            "leererSchreibtisch"
            , new Supporter(
                "Leerer Schreibtisch"
                , "https://leererschreibtisch.de/"
            )
        );
        $hashMap->put(
            "owner"
            , new Supporter(
                "Ahmet Camuka"
                , "http://camuka.de/"
            )
        );
        return $hashMap;
    }

    public function setSessionHandler(): bool {
        return session_set_save_handler(
            Didapptic::getServer()->query(SessionHandler::class)
        );
    }

    public static function getServer(): Server {
        return Didapptic::$server;
    }

    /**
     * returns a boolean that indicates whether the
     * whole application is installed
     *
     * @return bool
     */
    public function isInstalled(): bool {
        /** @var Installer $installer */
        $installer = Didapptic::getServer()->query(Installer::class);
        return $installer->isInstalled();
    }

}
