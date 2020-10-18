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

use Didapptic\Object\Option;
use doganoo\PHPUtil\Storage\PDOConnector;

/**
 * Class OptionManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class OptionRepository {

    private $connector = null;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function getOption(string $name, string $default = null): ?Option {
        $sql    = "SELECT name, value, create_ts FROM options where name = :name;";
        $option = new Option();
        $option->setName($name);
        $option->setValue($default);
        $option->setCreateTs(0);
        $connection = $this->connector->getConnection();
        $statement  = $connection->prepare($sql);
        if (!$statement) {
            return $option;
        }
        $statement->bindParam(":name", $name);
        $statement->execute();
        if ($statement->rowCount() === 0) {
            return $option;
        }
        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $nname    = $row[0];
            $value    = $row[1];
            $createTs = \intval($row[2]);

            $option->setName($nname);
            $option->setValue($value);
            $option->setCreateTs($createTs);
        }
        return $option;
    }

    public function addOption(string $key, string $value): bool {
        if ($this->exists($key)) {
            return $this->update($key, $value);
        }
        return $this->insert($key, $value);
    }

    private function exists(string $key): bool {
        $connection = $this->connector->getConnection();
        $sql        = "SELECT exists(SELECT value FROM options WHERE name = :name);";
        $statement  = $connection->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bindParam(":name", $key);
        $statement->execute();
        if ($statement->rowCount() === 0) {
            return false;
        }
        $row    = $statement->fetch(\PDO::FETCH_BOTH);
        $exists = $row[0];
        return $exists == "1";
    }

    public function update(string $name, string $value) {
        $sql       = "UPDATE options SET 
                    value = :value, 
                    create_ts = :create_ts
                WHERE name = :name";
        $statement = $this->connector->getConnection()->prepare($sql);
        $createTs  = (new \DateTime())->getTimestamp();
        $statement->bindParam(":value", $value);
        $statement->bindParam(":create_ts", $createTs);
        $statement->bindParam(":name", $name);

        $executed = $statement->execute();
        if ($executed) {
            return true;
        }
        return false;
    }

    public function insert(string $name, string $value): bool {
        $sql       = "INSERT INTO options (name, value, create_ts) values (:name, :value, :create_ts);";
        $createTs  = (new \DateTime())->getTimestamp();
        $statement = $this->connector->getConnection()->prepare($sql);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":value", $value);
        $statement->bindParam(":create_ts", $createTs);

        $executed = $statement->execute();
        if ($executed) {
            return true;
        }
        return false;
    }

}
