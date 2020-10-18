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
use Didapptic\Object\Token;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Storage\PDOConnector;
use doganoo\PHPUtil\Util\DateTimeUtil;
use PDO;

/**
 * Class TokenManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class TokenRepository {

    private $connector   = null;
    private $userManager = null;
    private $minHours    = 4;

    public function __construct(PDOConnector $connector, UserRepository $userManager) {
        $this->connector   = $connector;
        $this->userManager = $userManager;
        $this->connector->connect();
    }

    public function insert(string $token, int $userId): bool {
        $sql       = "insert into token (token, user_id,create_ts,active) values (:token, :user_id,:create_ts,:active);";
        $statement = $this->connector->prepare($sql);

        if (null === $statement) return false;

        $createTs = (new DateTime())->getTimestamp();
        $active   = 1;
        $statement->bindParam(":token", $token);
        $statement->bindParam(":user_id", $userId);
        $statement->bindParam(":create_ts", $createTs);
        $statement->bindParam(":active", $active);
        $executed = $statement->execute();
        return $executed;
    }

    public function deactivate(string $token): bool {
        $sql       = "update `token` set `active` = :active where `token` = :token;";
        $statement = $this->connector->prepare($sql);

        if (null === $statement) return false;

        $active = 0;
        $statement->bindParam(":active", $active);
        $statement->bindParam(":token", $token);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function getToken(string $token): ?Token {
        $sql = "
        select t.`id`
              , t.`token`
              , t.`user_id`
              , t.`create_ts`
              , t.`active`
                  from `token` t
          where t.`token` = :token
            and t.`active` = :active;";

        $active    = 1;
        $statement = $this->connector->prepare($sql);
        $statement->bindParam("token", $token);
        $statement->bindParam("active", $active);
        $statement->execute();

        $token = null;
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id          = $row[0];
            $tokenString = $row[1];
            $user        = $this->userManager->getUserById((int) $row[2]);
            $createTs    = $row[3];
            $active      = $row[4];

            $token = new Token();
            $token->setId((int) $id);
            $token->setToken($tokenString);
            $token->setUser($user);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $createTs);
            $token->setCreateTs($dateTime);
            $token->setActive(1 == $active);
        }

        return $token;
    }

    public function getOutdatedTokens(): ?ArrayList {
        $sql       = "select 
                        id
                        , token
                        , user_id
                        , create_ts
                        , active
                from token 
                  where active = :active
                  and create_ts < :create_ts;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return null;
        $active     = 1;
        $subtracted = DateTimeUtil::subtractHours($this->minHours);
        $subtracted = $subtracted->getTimestamp();
        $statement->bindParam("active", $active);
        $statement->bindParam("create_ts", $subtracted);
        $statement->execute();

        $list = new ArrayList();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id          = $row[0];
            $tokenstring = $row[1];
            $createTs    = $row[3];
            $active      = $row[4];

            $token = new Token();
            $token->setId((int) $id);
            $token->setToken($tokenstring);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $createTs);
            $token->setCreateTs($dateTime);
            $token->setActive(1 === $active);
            $list->add($token);
        }
        return $list;
    }

}
