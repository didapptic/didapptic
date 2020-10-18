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

namespace Didapptic\Repository;

use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;
use function json_decode;

/**
 * Class URLManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class URLRepository {

    public const TYPE_SCREENSHOTS = "screenshots";
    public const NAME_IPAD        = "ipad_screenshots";

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
        $this->connector->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function exists(int $appId, string $name): bool {
        $sql       = "SELECT `id` FROM `url` WHERE `app_id` = :app_id AND `name` = :name;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $statement->bindParam(":app_id", $appId);
        $statement->bindParam(":name", $name);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function update(
        int $appId
        , string $urls
        , string $name
        , int $createTs
    ): bool {
        $sql       = "UPDATE `url` SET `urls` = :urls, `create_ts` = :create_ts WHERE `app_id` = :app_id AND `name` = :name;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $statement->bindParam(":urls", $urls);
        $statement->bindParam(":create_ts", $createTs);
        $statement->bindParam(":app_id", $appId);
        $statement->bindParam(":name", $name);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function insert(
        int $appId
        , string $urls
        , string $name
        , int $createTs
    ): bool {
        $sql       = "INSERT INTO `url` 
                        (
                            `app_id`
                            , `urls`
                            , `name`
                            , `create_ts`
                        ) VALUES (
                            :appId
                            , :urls
                            , :name
                            , :create_ts
                        );";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;

        $statement->bindParam(":appId", $appId);
        $statement->bindParam(":urls", $urls);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":create_ts", $createTs);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function getIconURL(int $appId): ?string {
        $iconUrl = $this->getURLPerApp($appId, "icon");
        if (null === $iconUrl) return null;
        return json_decode($iconUrl, true);
    }

    private function getURLPerApp(int $appId, string $name): ?string {
        $sql       = "SELECT `urls` from `url` WHERE `app_id` = :app_id AND `name` = :name;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return null;

        $statement->bindParam("app_id", $appId);
        $statement->bindParam("name", $name);
        $statement->execute();

        return $row = $statement->fetch(PDO::FETCH_BOTH)[0] ?? null;
    }

    public function getIosPrivacyURL(int $appId): ?string {
        return $this->getURLPerApp($appId, "ios_privacy");
    }

    public function getScreenshotURLs(int $appId): array {
        $sql       = "SELECT `urls` FROM `url` WHERE `app_id` = :app_id AND `name` IN(:name, :ipad_name);";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return [];
        }
        $name     = URLRepository::TYPE_SCREENSHOTS;
        $iPadName = URLRepository::NAME_IPAD;

        $statement->bindParam("app_id", $appId);
        $statement->bindParam("name", $name);
        $statement->bindParam("ipad_name", $iPadName);
        $statement->execute();

        $screenshotUrls = "";
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $screenshotUrls = $row[0];
        }
        if ($statement->rowCount() == 0) {
            return [];
        }
        $result = json_decode($screenshotUrls, true);
        return true === is_array($result) ? $result : [];
    }

}
