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
use doganoo\PHPUtil\Util\DateTimeUtil;
use PDO;

/**
 * Class SessionManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class SessionRepository {

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function get(string $id): ?string {
        $sql       = "SELECT `data` FROM `session` WHERE `id` = :id";
        $statement = $this->connector->prepare($sql);

        $statement->bindParam("id", $id);
        $executed = $statement->execute();
        if (false === $executed) return null;
        if (true === $this->hasErrors($statement->errorCode())) return null;
        $row  = $statement->fetch(PDO::FETCH_BOTH);
        $data = $row["data"];
        return (string) $data;
    }

    private function hasErrors(string $code): bool {
        return $code !== "00000";
    }

    public function replace(string $id, string $data): bool {
        $sql       = "REPLACE INTO `session`(`id`, `data`, `update_ts`) VALUES (:id, :thedata, :updatets)";
        $statement = $this->connector->prepare($sql);

        $updateTs = DateTimeUtil::formatMysqlDateTime(new DateTime());
        $statement->bindParam("id", $id);
        $statement->bindParam("thedata", $data);
        $statement->bindParam("updatets", $updateTs);
        $executed = $statement->execute();
        return true === $executed && false === $this->hasErrors($statement->errorCode());
    }

}
