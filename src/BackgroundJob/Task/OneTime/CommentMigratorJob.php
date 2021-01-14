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

namespace Didapptic\BackgroundJob\Task\OneTime;

use Didapptic\Didapptic;
use Didapptic\Object\App;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Repository\CommentRepository;
use doganoo\Backgrounder\Task\Task;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

/**
 * Class CommentMigratorJob
 *
 * @package Didapptic\BackgroundJob\Task\OneTime
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class CommentMigratorJob extends Task {

    /** @var PDOConnector */
    private $connector;
    /** @var CommentRepository */
    private $commentManager;
    /** @var AppRepository */
    private $appManager;

    public function __construct(
        PDOConnector $connector
        , CommentRepository $commentManager
        , AppRepository $appManager
    ) {
        $this->connector      = $connector;
        $this->commentManager = $commentManager;
        $this->appManager     = $appManager;
        $this->connector->connect();
    }

    protected function onAction(): void {
        //silence is golden
    }

    protected function action(): bool {
        $this->migrateDidacticComment();
        $this->migrateAnmerkungen();
        $this->migratePrivacyComment();
        return true;
    }

    private function migrateDidacticComment(): void {
        if (!$this->tableExists("DidKommentar")) return;
        $array     = [];
        $sql       = "select
                  ad.AppID
                  , DK.DidKommentar
                from App_DidKommentar ad
                  left join DidKommentar DK on ad.DidKommentarID = DK.DidKommentarID;";
        $statement = $this->connector->prepare($sql);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id      = $row[0];
            $comment = $row[1];
            if ($id === "" || $id === null || $comment === "" || $comment === null) continue;
            if (false === $this->appExists($id)) continue;
            $array[$id] = $comment;
        }

        foreach ($array as $id => $comment) {
            $inserted = $this->commentManager->insertComment($comment, \intval($id), CommentRepository::DIDACTIC_COMMENT);
            if ($inserted) {
                $sql       = "delete from DidKommentar where DidKommentarID in (select App_DidKommentar.DidKommentarID from App_DidKommentar where AppID = :app_id);";
                $statement = $this->connector->prepare($sql);
                $statement->bindParam("app_id", $id);
                $statement->execute();
            }
        }
    }

    private function tableExists(string $tableName): bool {
        $sql        = "SELECT count(*) FROM information_schema.TABLES WHERE (TABLE_SCHEMA = :schema_name) AND (TABLE_NAME = :table_name);";
        $statement  = $this->connector->prepare($sql);
        $schemaName = $this->connector->getSchema();
        $statement->bindParam("schema_name", $schemaName);
        $statement->bindParam("table_name", $tableName);
        $statement->execute();

        if (0 === $statement->rowCount()) return false;
        $row = $statement->fetch(PDO::FETCH_BOTH);
        return $row[0] != 0;
    }

    private function appExists(int $id): bool {
        $apps = Didapptic::getServer()->getAppsFromCache();
        /** @var App $app */
        foreach ($apps as $app) {
            if ($id === $app->getId()) return true;
        }

        return false;
    }

    private function migrateAnmerkungen(): void {
        if (!$this->tableExists("Anmerkungen")) return;
        if (!$this->tableExists("App_Anmerkungen")) return;
        $array     = [];
        $sql       = "select
  AppID,
  Anmerkungen
from Anmerkungen a left join App_Anmerkungen A3 on a.AnmerkungenID = A3.AnmerkungenID;";
        $statement = $this->connector->prepare($sql);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id      = $row[0];
            $comment = $row[1];
            if ($id === "" || $id === null || $comment === "" || $comment === null) continue;
            if (false === $this->appExists($id)) continue;
            $array[$id] = $comment;
        }

        foreach ($array as $id => $comment) {
            $inserted = $this->commentManager->insertComment($comment, \intval($id), CommentRepository::DIDACTIC_REMARK);
            if ($inserted) {
                $sql       = "delete from Anmerkungen where AnmerkungenID in (select AnmerkungenID from App_Anmerkungen where AppID = :app_id);";
                $statement = $this->connector->prepare($sql);
                $statement->bindParam("app_id", $id);
                $statement->execute();
            }
        }
    }

    private function migratePrivacyComment(): void {
        if (!$this->tableExists("PrivacyComment")) return;
        if (!$this->tableExists("App_PrivacyComment")) return;
        $array     = [];
        $sql       = "select
  AppID,
  PrivacyComment
from PrivacyComment a left join App_PrivacyComment Comment2 on a.PrivacyCommentID = Comment2.PrivacyCommentID;";
        $statement = $this->connector->prepare($sql);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id      = $row[0];
            $comment = $row[1];
            if ($id === "" || $id === null || $comment === "" || $comment === null) continue;
            if (false === $this->appExists($id)) continue;
            $array[$id] = $comment;
        }

        foreach ($array as $id => $comment) {
            $inserted = $this->commentManager->insertComment($comment, \intval($id), CommentRepository::PRIVACY_COMMENT);
            if ($inserted) {
                $sql       = "delete from PrivacyComment where PrivacyCommentID in (select PrivacyCommentID from App_PrivacyComment where AppID = :app_id);";
                $statement = $this->connector->prepare($sql);
                $statement->bindParam("app_id", $id);
                $statement->execute();
            }
        }
    }

    protected function onClose(): void {

    }

}
