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
use Didapptic\Object\Material;
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Storage\PDOConnector;

/**
 * Class MaterialManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MaterialRepository {

    private $connector   = null;
    private $fileManager = null;

    public function __construct(
        PDOConnector $connector
        , FileRepository $fileManager
    ) {
        $this->connector   = $connector;
        $this->fileManager = $fileManager;
        $this->connector->connect();
    }

    public function insert(Material $material): ?int {
        $sql       = "insert into material (
                                    description
                                    , date
                                    , creator_id
                                    , create_ts
                                    , name
                                    , password
                                  ) values (
                                    :description
                                    , :date
                                    , :creator_id
                                    , :create_ts
                                    , :name
                                    , :password
                                    )";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return null;
        }

        $description = $material->getDescription();
        $date        = $material->getDate();
        $creatorId   = $material->getCreatorId();
        $createTs    = $material->getCreateTs();
        $name        = $material->getName();
        $password    = $material->getPassword();

        $statement->bindParam(":description", $description);
        $statement->bindParam(":date", $date);
        $statement->bindParam(":creator_id", $creatorId);
        $statement->bindParam(":create_ts", $createTs);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":password", $password);

        $executed = $statement->execute();
        if (false === $executed) return null;

        $lastInsertId = $this->connector->getLastInsertId();
        $material->setId((int) $lastInsertId);
        if (null === $material->getFiles()) return (int) $lastInsertId;

        /** @var File $file */
        foreach ($material->getFiles() as $file) {
            $inserted = $this->fileManager->insert($file);
            if (false === $inserted) {
                FileLogger::debug("could not insert {$file->getName()}");
                continue;
            }

            $this->connectToFile(
                $material->getId()
                , $file->getId()
                , $material->getCreatorId()
                , $material->getCreateTs()
            );

        }
        return (int) $lastInsertId;
    }

    private function connectToFile(int $materialId, int $fileId, int $creatorId, int $createTs): bool {
        $sql       = "insert into material_file (
                                      m_id
                                    , f_id
                                    , creator_id
                                    , create_ts
                                  ) values (
                                     :m_id
                                    , :f_id
                                    , :creator_id
                                    , :create_ts
                                    )";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }

        $statement->bindParam(":m_id", $materialId);
        $statement->bindParam(":f_id", $fileId);
        $statement->bindParam(":creator_id", $creatorId);
        $statement->bindParam(":create_ts", $createTs);

        $executed = $statement->execute();
        if (false === $executed) return false;

        return true;
    }

    public function getAll() {
        $list = [];
        $sql  = "select
                      m.id
                      , m.description
                      , m.date
                      , m.creator_id
                      , m.create_ts
                      , m.name
                      , m.password
                    from material m
                order by m.create_ts desc;";

        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return $list;
        }
        $statement->execute();
        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $id          = $row[0];
            $description = $row[1];
            $date        = $row[2];
            $creatorId   = $row[3];
            $createTs    = $row[4];
            $name        = $row[5];
            $password    = $row[6];

            $material = new Material();
            $material->setId((int) $id);
            $material->setDescription($description);
            $material->setDate((int) $date);
            $material->setCreatorId((int) $creatorId);
            $material->setCreateTs((int) $createTs);
            $material->setName($name);
            $material->setPassword($password);

            $mfList = $this->getFilesForMaterial((int) $id);

            foreach ($mfList as $l) {
                $files = $this->fileManager->getById((int) $l["f_id"]);
                $material->addFile($files);
            }
            $list[] = $material;
        }
        return $list;
    }

    private function getFilesForMaterial(int $materialId): array {
        $list = [];
        $sql  = "SELECT
                      mf.`id`
                      , mf.`m_id`
                      , mf.`f_id`
                      , mf.`creator_id`
                      , mf.`create_ts`
                    from `material_file` mf
                        where mf.`m_id` = :m_id
                order by mf.`create_ts` desc;";

        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return $list;
        }
        $statement->bindParam("m_id", $materialId);
        $statement->execute();
        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $mfId      = $row[0];
            $mId       = $row[1];
            $fId       = $row[2];
            $creatorId = $row[3];
            $createTs  = $row[4];

            $list[] = [
                "mf_id"        => $mfId
                , "m_id"       => $mId
                , "f_id"       => $fId
                , "creator_id" => $creatorId
                , "create_ts"  => $createTs
            ];
        }
        return $list;
    }

    public function get(int $materialId): ?Material {
        $sql = "select
                      m.id
                      , m.description
                      , m.date
                      , m.creator_id
                      , m.create_ts
                      , m.name
                      , m.password
                    from material m
                        where m.id = :id
                order by m.create_ts desc;";

        $statement = $this->connector->prepare($sql);

        if (null === $statement) {
            return null;
        }
        $statement->bindParam("id", $materialId);
        $statement->execute();

        if (0 === $statement->rowCount()) return null;

        $material = new Material();
        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $id          = $row[0];
            $description = $row[1];
            $date        = $row[2];
            $creatorId   = $row[3];
            $createTs    = $row[4];
            $name        = $row[5];
            $password    = $row[6];

            $material->setId((int) $id);
            $material->setDescription($description);
            $material->setDate((int) $date);
            $material->setCreatorId((int) $creatorId);
            $material->setCreateTs((int) $createTs);
            $material->setName($name);
            $material->setPassword($password);

            FileLogger::debug(json_encode($material));

            $mfList = $this->getFilesForMaterial((int) $id);

            foreach ($mfList as $l) {
                $files = $this->fileManager->getById((int) $l["f_id"]);
                $material->addFile($files);
            }
        }

        return $material;
    }

    public function disconnectFromFile(Material $material, File $file): bool {
        $sql       = "delete from material_file where m_id = :id and f_id = :f_id;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }

        $materialId = $material->getId();
        $fileId     = $file->getId();

        $statement->bindParam(":id", $materialId);
        $statement->bindParam(":f_id", $fileId);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function exists(int $materialId) {
        $sql       = "select id from material where id = :id ;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }
        $statement->bindParam(":id", $materialId);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function delete(int $materialId) {
        $files = $this->getFilesForMaterial($materialId);

        $sql       = "delete from material_file where m_id = :id ;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }
        $statement->bindParam(":id", $materialId);
        $statement->execute();

        $sql       = "delete from material where id = :id ;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }
        $statement->bindParam(":id", $materialId);
        $statement->execute();

        $deleted = $statement->rowCount() > 0;

        if (false === $deleted) return false;

        foreach ($files as $file) {
            $this->fileManager->delete((int) $file['f_id']);
        }

        return true;
    }

}
