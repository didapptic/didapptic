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

use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;
use doganoo\PHPUtil\Exception\FileNotFoundException;
use doganoo\PHPUtil\Exception\NoPathException;
use doganoo\PHPUtil\System\Properties;
use Exception;
use function strpos;

/**
 * Class Environment
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Environment {

    /** @var Properties */
    private $sysProperties;
    /** @var HashMap */
    private $cache;

    public function __construct(Properties $sysProperties) {
        $this->sysProperties = $sysProperties;
        $this->cache         = new HashMap();
    }

    public function read(string $name): string {
        return (string) $this->getProperty($name);
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return null|string
     */
    private function getProperty(string $name, $default = null): ?string {
        try {
            if (true === $this->cache->containsKey($name)) {
                return $this->cache->get($name);
            }
            $property = $this->sysProperties->read($name);
            $this->cache->put($name, $property);
            return $property;
        } catch (Exception $exception) {
            return $default;
        }

        return $default;
    }

    /**
     * @return null|string
     * @throws FileNotFoundException
     * @throws NoPathException
     */
    public function getGermanDatePattern(): ?string {
        return $this->getProperty("system.date.pattern.de.display");
    }

    /**
     * @return null|string
     * @throws FileNotFoundException
     * @throws NoPathException
     */
    public function getNewUserRegistrationPassword(): ?string {
        return $this->getProperty("system.user.new.registration.password");
    }

    /**
     * @return null|string
     * @throws FileNotFoundException
     * @throws NoPathException
     */
    public function getNewUserRegistrationUser(): ?string {
        return $this->getProperty("system.user.new.registration.user");
    }

    /**
     * @return null|string
     * @throws FileNotFoundException
     * @throws NoPathException
     */
    public function getMaterialViewPassword(): ?string {
        return $this->getProperty("system.user.view.material.password");
    }

    /**
     * @return null|string
     * @throws FileNotFoundException
     * @throws NoPathException
     */
    public function getMaterialViewUser(): ?string {
        return $this->getProperty("system.user.view.material.user");
    }

    /**
     * @return bool
     * @throws FileNotFoundException
     * @throws NoPathException
     */
    public function isPageRestricted(): bool {
        return (bool) ($this->isTest() || $this->isProduction());
    }

    /**
     * @return bool
     */
    public function isTest(): bool {
        $production = $this->isProduction();
        if (false !== strpos($_SERVER["HTTP_HOST"], "didapptic.dogan-ucar.de") && false !== strpos($_SERVER["SERVER_NAME"], "didapptic.dogan-ucar.de") && !$production) return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool {
        $production = false === $this->isDebug();
        if (false !== strpos($_SERVER["HTTP_HOST"], "didapptic.com") && false !== strpos($_SERVER["SERVER_NAME"], "didapptic.com") && $production) return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool {
        return (bool) $this->getProperty("system.settings.mode.debug");
    }

    /**
     * @return int
     */
    public function getChunkSize(): int {
        return (int) $this->getProperty("system.settings.app.chunks.size");
    }

    /**
     * @return array
     */
    public function getDatabaseProperties(): array {
        $host     = $this->getProperty("database.host");
        $name     = $this->getProperty("database.schema.name");
        $userName = $this->getProperty("database.username");
        $password = $this->getProperty("database.password");
        $charSet  = $this->getProperty("database.charset");
        return [
            "servername" => $host
            , "dbname"   => $name
            , "username" => $userName
            , "password" => $password
            , "charset"  => $charSet
        ];
    }

    public function isHttps(): bool {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            return true;
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            return true;
        }
        return false;
    }

    public function getLogLevel(): string {
        return (string) $this->getProperty("system.settings.mode.log.level");
    }

    public function getWordPressURL(): ?string {
        return $this->sysProperties->read("url.backend.wordpress");
    }

    public function getWPApplicationPassword(): string {
        return (string) $this->sysProperties->read("password.application.backend.wordpress");
    }

    public function isSecure(): bool {
        return (bool) ($this->isProduction() || $this->isTest());
    }

    public function getEnvironmentDescription(): ?string {
        $test       = $this->isTest();
        $dev        = $this->isDev();
        $production = $this->isProduction();

        if ($test) return "TEST";
        if ($dev) return "DEVELOPMENT";
        if ($production) return "PRODUCTION";
        return null;
    }

    /**
     * @return bool
     */
    public function isDev(): bool {
        $production = $this->isProduction();
        if (false !== strpos($_SERVER["HTTP_HOST"], "localhost") && false !== strpos($_SERVER["SERVER_NAME"], "localhost") && !$production) return true;
        if (false !== strpos($_SERVER["HTTP_HOST"], "didapptic.local") && false !== strpos($_SERVER["SERVER_NAME"], "didapptic.local") && !$production) return true;
        return false;
    }


}
