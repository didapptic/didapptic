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

use DateTime;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

/**
 * Class DeviceManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class DeviceRepository {

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function exists(string $key): bool {
        $sql       = "SELECT exists(SELECT name FROM device WHERE name = :name);";
        $statement = $this->connector->prepare($sql);
        $statement->bindParam(":name", $key);
        $statement->execute();
        if ($statement->rowCount() === 0) {
            return false;
        }
        $row    = $statement->fetch(PDO::FETCH_BOTH);
        $exists = $row[0];
        return $exists == "1";
    }

    public function add(int $appId, string $name): bool {
        $sql       = "INSERT INTO device (name, app_id, create_ts) values (:name, :app_id, :create_ts);";
        $createTs  = (new DateTime())->getTimestamp();
        $statement = $this->connector->prepare($sql);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":app_id", $appId);
        $statement->bindParam(":create_ts", $createTs);

        $executed = $statement->execute();
        if ($executed) {
            return true;
        }
        return false;
    }

    public function getDevicesByAppId(int $appId): array {
        $array = [];
        $sql   = "    select 
                            d.id
                          , d.name
                          , d.app_id
                          , d.create_ts
                    from device d 
                    where d.app_id = :app_id;";

        $statement = $this->connector->prepare($sql);

        $statement->bindParam("app_id", $appId);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id   = $row[0];
            $name = $row[1];
            if ($id === "" || $name === "") {
                continue;
            }
            $array[$id] = $name;
        }
        return $array;
    }

}
