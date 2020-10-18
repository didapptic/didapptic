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

namespace Didapptic\Manager;

use DateTime;
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Storage\PDOConnector;
use doganoo\PHPUtil\Util\DateTimeUtil;
use PDO;

/**
 * TODO use repository service class instead of querying data directly
 *
 * Class SessionManager
 *
 * @package storage
 */
class SessionManager {

    private $connector = null;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function get(string $id): ?string {
        $sql       = "SELECT `data` FROM `session` WHERE `id` = :id";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return null;
        $statement->bindParam("id", $id);
        $executed = $statement->execute();
        if (false === $executed) return null;
        if (true === $this->hasErrors($statement->errorCode())) return null;
        $row = $statement->fetch(PDO::FETCH_BOTH);
        if (0 === $statement->rowCount()) {
            FileLogger::debug("no rows found for $id");
            return null;
        }
        $data = $row["data"];
        return (string) $data;
    }

    public function getAll(): ?array {
        $sql       = "SELECT `id`, `data`, `update_ts` FROM `session`;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return null;
        $executed = $statement->execute();
        if (false === $executed) return null;
        if (true === $this->hasErrors($statement->errorCode())) return null;
        $result = [];
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $result[] = $row;
        }
        return $result;
    }

    public function replace(string $id, string $data): bool {

        $exists = null !== $this->get($id);

        $sql = "INSERT INTO `session`(`id`, `data`, `update_ts`) VALUES (:id, :thedata, :update_ts)";

        if (true === $exists) {
            $sql = "UPDATE `session` 
                        SET `data` = :thedata
                        , `update_ts` = :update_ts
                    WHERE `id` = :id
                        ;";
        }

        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $updateTs = DateTimeUtil::formatMysqlDateTime(new DateTime());
        $statement->bindParam("id", $id);
        $statement->bindParam("thedata", $data);
        $statement->bindParam("update_ts", $updateTs);
        $executed = $statement->execute();
        return true === $executed && false === $this->hasErrors($statement->errorCode());
    }

    public function deleteById(string $id): bool {
        $sql       = "DELETE FROM `session` WHERE `id` = :id";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $statement->bindParam("id", $id);
        $executed = $statement->execute();
        return true === $executed && false === $this->hasErrors($statement->errorCode());
    }

    public function deleteByLastUpdate(int $maxLifeTime): bool {
        $sql       = "DELETE FROM `session` WHERE `update_ts` = :updatets";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $updateTs = (new DateTime())->getTimestamp() - $maxLifeTime;
        $statement->bindParam("updatets", $updateTs);
        $executed = $statement->execute();
        return true === $executed && false === $this->hasErrors($statement->errorCode());
    }

    private function hasErrors(string $code): bool {
        return $code !== "00000";
    }

}
