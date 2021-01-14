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

/**
 * Class TagManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class TagRepository {

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function getTags(): array {
        $array     = [];
        $sql       = "select `id`, `tag` from `tag` order by `tag` asc;";
        $statement = $this->connector->prepare($sql);
        $statement->execute();
        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $id   = $row[0];
            $name = $row[1];
            if ($id === "" || $name === "") {
                continue;
            }
            $array[$id] = $name;
        }
        return $array;
    }

    public function getTagsByAppId(int $appId): array {
        $array     = [];
        $sql       = "  select 
                          t.`id`
                        , t.`tag` 
                  from `tag` t
                    left join `app_tag` AT2 on t.`id` = AT2.`tag_id`
                    left join `app` A on AT2.`app_id` = A.`id`
                  where A.`id` = :app_id
                  order by `tag` asc;";
        $statement = $this->connector->prepare($sql);

        $statement->bindParam("app_id", $appId);
        $statement->execute();
        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $id   = $row[0];
            $name = $row[1];
            if ($id === "" || $name === "") {
                continue;
            }
            $array[$id] = $name;
        }
        return $array;
    }

    public function exists(string $tag): bool {
        $sql       = "select exists(select `id` from `tag` where `id` = :tagId)";
        $statement = $this->connector->prepare($sql);
        $statement->bindParam(":tagId", $tag);
        $statement->execute();
        $row    = $statement->fetch(\PDO::FETCH_BOTH);
        $exists = $row[0];
        return $exists === "1";
    }


    public function insert(string $tag): ?int {
        $sql = "insert into `tag` (
                                    `tag`
                                  ) values (
                                    :tag
                                    )";

        $statement = $this->connector->prepare($sql);

        $statement->bindParam(":tag", $tag);

        $executed = $statement->execute();
        if (false === $executed) return null;

        $lastInsertId = $this->connector->getLastInsertId();

        return (int) $lastInsertId;
    }

}
