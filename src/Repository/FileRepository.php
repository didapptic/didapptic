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

use Didapptic\Object\File;
use doganoo\PHPUtil\Storage\PDOConnector;

/**
 * Class FileManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class FileRepository {

    private $connector = null;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function insert(File &$file): bool {
        $sql       = "insert into file (
                                    name
                                    , mime_type
                                    , hash
                                    , path
                                    , creator_id
                                    , create_ts
                                    , size
                                  ) values (
                                    :name
                                    , :mime_type
                                    , :hash
                                    , :path
                                    , :creator_id
                                    , :create_ts
                                    , :size
                                    )";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }

        $name      = $file->getName();
        $mimeType  = $file->getMimeType();
        $hash      = $file->getHash();
        $path      = $file->getPath();
        $creatorId = $file->getCreatorId();
        $createTs  = $file->getCreateTs();
        $size      = $file->getSize();

        $statement->bindParam(":name", $name);
        $statement->bindParam(":mime_type", $mimeType);
        $statement->bindParam(":hash", $hash);
        $statement->bindParam(":path", $path);
        $statement->bindParam(":creator_id", $creatorId);
        $statement->bindParam(":create_ts", $createTs);
        $statement->bindParam(":size", $size);

        $executed = $statement->execute();
        if (false === $executed) return false;

        $lastInsertId = $this->connector->getLastInsertId();
        $file->setId((int) $lastInsertId);

        return true === is_numeric($lastInsertId);

    }

    public function delete(int $fileId) {
        $sql       = "delete from file where id = :id ;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }
        $statement->bindParam(":id", $fileId);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function getById(int $id): ?File {
        $sql = "select
                      f.id
                      , f.name
                      , f.mime_type
                      , f.hash
                      , f.path
                      , f.creator_id
                      , f.create_ts
                      , f.size
                    from file f
                        where f.id = :f_id
                order by f.create_ts desc;";

        $statement = $this->connector->prepare($sql);
        if (null === $statement) return null;

        $statement->bindParam("f_id", $id);
        $statement->execute();
        $file = null;

        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $id        = $row[0];
            $name      = $row[1];
            $mimeType  = $row[2];
            $hash      = $row[3];
            $path      = $row[4];
            $creatorId = $row[5];
            $createTs  = $row[6];
            $size      = $row[7];

            $file = new File();
            $file->setId((int) $id);
            $file->setName($name);
            $file->setMimeType($mimeType);
            $file->setHash($hash);
            $file->setPath($path);
            $file->setCreateTs((int) $createTs);
            $file->setCreatorId((int) $creatorId);
            $file->setSize((int) $size);
        }
        return $file;
    }

}
