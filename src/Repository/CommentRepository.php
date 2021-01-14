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
use doganoo\PHPUtil\Util\DateTimeUtil;
use function htmlentities;
use function strlen;
use function substr;

/**
 * Class CommentManager
 *
 * TODO this is not a good solution!
 *
 * @package storage
 */
class CommentRepository {

    public const DIDACTIC_REMARK  = "didactic_remark";
    public const DIDACTIC_COMMENT = "didactic_comment";
    public const PRIVACY_COMMENT  = "privacy_comment";

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
    }

    public function insertComment(string $comment, int $appId, string $type): bool {
        if ($comment === "") {
            return false;
        }
        if (!$this->validType($type)) {
            return false;
        }
        if (strlen($comment) > 65535) {
            FileLogger::info("$type for $appId is to long. shortening it to 65534 characters");
            $comment = substr($comment, 0, 65535 - 1);
        }
        $sql       = "insert into comment (app_id, type, text, create_ts) VALUES (:app_id, :type, :text ,:create_ts)";
        $statement = $this->connector->prepare($sql);

        $createTs = DateTimeUtil::getUnixTimestamp();
        $comment  = htmlentities($comment);
        $statement->bindParam(":app_id", $appId);
        $statement->bindParam(":type", $type);
        $statement->bindParam(":text", $comment);
        $statement->bindParam(":create_ts", $createTs);
        $executed = $statement->execute();

        return $executed;
    }

    private function validType(string $type): bool {
        return $type === CommentRepository::DIDACTIC_REMARK
            || $type === CommentRepository::PRIVACY_COMMENT
            || $type === CommentRepository::DIDACTIC_COMMENT;
    }

    public function updateComment(string $comment, int $appId, string $type): bool {
        if ($comment === "") {
            return false;
        }
        if (strlen($comment) > 65535) {
            FileLogger::info("text for $appId is to long. shortening it to 65534 characters");
            $comment = substr($comment, 0, 65535 - 1);
        }
        $sql       = "UPDATE `comment` SET `text` = :text WHERE `app_id` = :app_id AND `type` = :type;";
        $statement = $this->connector->prepare($sql);

        $statement->bindParam(":text", $comment);
        $statement->bindParam(":app_id", $appId);
        $statement->bindParam(":type", $type);
        $executed = $statement->execute();
        return $executed;
    }

}
