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

use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

/**
 * Class StudySubjectManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class StudySubjectRepository {

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function getSubjects(): array {
        $array     = [];
        $sql       = "select 
                        f.id
                      , f.subject 
                from subject f
                  where f.active = :active
                order by f.subject asc;";
        $statement = $this->connector->prepare($sql);

        $active = 1;
        $statement->bindParam("active", $active);
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

    public function getSubjectsByAppId(int $appId): array {
        $array = [];
        $sql   = "
                        SELECT 
                            f.`id`
                          , f.`subject` 
                        FROM `subject` f 
                            LEFT JOIN `app_subject` asu ON f.`id` = asu.`subject_id`
                            LEFT JOIN `app` a ON asu.`app_id` = a.`id`
                        WHERE a.`id` = :app_id
                        ORDER BY f.`subject` ASC;";

        $statement = $this->connector->prepare($sql);

        $statement->bindParam("app_id", $appId);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id      = $row[0];
            $subject = $row[1];

            FileLogger::debug($id);
            FileLogger::debug($subject);
            if ($id === "" || $subject === "") {
                continue;
            }
            $array[$id] = $subject;
        }
        return $array;
    }

    public function exists(string $subjectId): bool {
        $sql       = "select 
                    exists(select f.id 
                            from subject f 
                          where f.id = :sub_id
                          and f.active = :active
                        )";
        $statement = $this->connector->prepare($sql);
        $active = 1;
        $statement->bindParam(":sub_id", $subjectId);
        $statement->bindParam(":active", $active);
        $statement->execute();
        $row    = $statement->fetch(PDO::FETCH_BOTH);
        $exists = $row[0];
        return $exists === "1";
    }

    public function insert(string $subject): ?int {
        $sql       = "insert into `subject` (
                                    `subject`
                                    , `active`
                                  ) values (
                                    :faecher
                                    , :active
                                    )";
        $statement = $this->connector->prepare($sql);

        $active = 1;

        $statement->bindParam(":faecher", $subject);
        $statement->bindParam(":active", $active);

        $executed = $statement->execute();
        if (false === $executed) return null;

        $lastInsertId = $this->connector->getLastInsertId();

        return (int) $lastInsertId;
    }

}
