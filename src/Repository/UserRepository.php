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
use Didapptic\Didapptic;
use Didapptic\Object\Registrant;
use Didapptic\Object\User;
use Didapptic\Service\User\Role\RoleService;
use doganoo\PHPAlgorithms\Datastructure\Graph\Tree\BinarySearchTree;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Storage\PDOConnector;
use doganoo\PHPUtil\Util\DateTimeUtil;
use PDO;
use function intval;
use function json_encode;

/**
 * Class UserManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class UserRepository {

    /** @var PDOConnector */
    private $connector;
    /** @var StudySubjectRepository */
    private $subjectManager;
    /** @var RoleService */
    private $roleService;

    public function __construct(
        PDOConnector $connector
        , StudySubjectRepository $subjectManager
        , RoleService $roleService
    ) {
        $this->connector      = $connector;
        $this->subjectManager = $subjectManager;
        $this->roleService    = $roleService;
        $this->connector->connect();
    }

    public function userNameExists(?string $userName): bool {
        if (null === $userName) return false;
        $exists    = false;
        $sql       = "select exists(select id from user where name = :name);";
        $statement = $this->connector->prepare($sql);

        $statement->bindParam("name", $userName);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $exists = $row[0] == "1";
        }
        return $exists;
    }

    public function emailExists(?string $email): bool {
        if (null === $email) return false;
        $exists    = false;
        $sql       = "select exists(select id from user where email = :email);";
        $statement = $this->connector->prepare($sql);
        $statement->bindParam("email", $email);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $exists = $row[0] == "1";
        }
        return $exists;
    }

    public function insert(Registrant $registrant, string $confirmationToken): ?int {
        $firstName       = $registrant->getFirstName();
        $lastName        = $registrant->getLastName();
        $userName        = $registrant->getName();
        $email           = $registrant->getEmail();
        $password        = $registrant->getPassword();
        $websiteUrl      = $registrant->getWebsiteURL();
        $wantsNewsletter = $registrant->isWantsNewsletter();
        $subjects        = $registrant->getSubjects();

        $logged = $this->logRegistration($registrant, $confirmationToken);
        if (false === $logged) return null;
        $sql             = "insert into user (
                        first_name
                      , last_name
                      , name
                      , email
                      , password
                      , website_url
                      , newsletter
                      , register_token
                      , confirmation_token
                      , created_at)
                values (
                  :first_name
                , :last_name
                , :name
                , :email
                , :password
                , :website_url
                , :newsletter
                , :register_token
                , :confirmation_token
                , :created_at
                )";
        $dateTime        = new DateTime();
        $now             = $dateTime->getTimestamp();
        $wantsNewsletter = true === $wantsNewsletter ? 1 : 0;
        $statement       = $this->connector->prepare($sql);
        $token           = null;
        $statement->bindParam(":first_name", $firstName);
        $statement->bindParam(":last_name", $lastName);
        $statement->bindParam(":name", $userName);
        $statement->bindParam(":email", $email);
        $statement->bindParam(":password", $password);
        $statement->bindParam(":website_url", $websiteUrl);
        $statement->bindParam(":newsletter", $wantsNewsletter);
        $statement->bindParam(":register_token", $token);
        $statement->bindParam(":confirmation_token", $confirmationToken);
        $statement->bindParam(":created_at", $now);
        $statement->execute();
        $lastInsertId = (int) $this->connector->getLastInsertId();

        if (0 === $lastInsertId) return null;

        $this->setRoles($lastInsertId);

        foreach ($subjects as $subject) {
            if ($this->subjectManager->exists($subject)) {
                $this->addSubject($lastInsertId, $subject);
            }
        }

        Didapptic::getServer()->clearCaches();
        return $lastInsertId;
    }

    /**
     * @param Registrant $registrant
     * @param string     $confirmationToken
     *
     * @return bool
     */
    private function logRegistration(Registrant $registrant, string $confirmationToken): bool {
        $sql       = "insert into registration_log (token, confirmation_token, user_info, create_ts) VALUES (:token, :confirmation_token, :user_info, :create_ts)";
        $statement = $this->connector->prepare($sql);
        $now       = (new DateTime())->getTimestamp();
        $json      = json_encode($registrant);
        $token     = null;
        $statement->bindParam("token", $token);
        $statement->bindParam("confirmation_token", $confirmationToken);
        $statement->bindParam("user_info", $json);
        $statement->bindParam("create_ts", $now);
        $executed = $statement->execute();
        return $executed;
    }

    private function setRoles(int $id, int $roleId = 2): bool {
        $sql       = "insert into user_role (role_id, user_id, create_ts) values (:role_id, :user_id, :create_ts);";
        $statement = $this->connector->prepare($sql);
        $timestamp = DateTimeUtil::getUnixTimestamp();
        $statement->bindParam("role_id", $roleId);
        $statement->bindParam("user_id", $id);
        $statement->bindParam("create_ts", $timestamp);
        Didapptic::getServer()->clearCaches();
        return $statement->execute();
    }

    private function addSubject(int $userId, int $subjectId): void {
        $sql       = "INSERT INTO `user_subject`(`subject_id`, `user_id`) VALUES (:faecher_id, :user_id);";
        $statement = $this->connector->prepare($sql);
        $statement->bindParam(":faecher_id", $subjectId);
        $statement->bindParam(":user_id", $userId);
        $statement->execute();
    }

    public function getUserById(int $userId): ?User {
        $sql       = "select id
                      , name
                      , password
                      , first_name
                      , last_name
                      , wp_user_id
                      , email
                from user 
                  where id = :id;";
        $statement = $this->connector->prepare($sql);
        $statement->bindParam(":id", $userId);

        $statement->execute();
        if ($statement->rowCount() === 0) {
            return null;
        }
        $user = new User();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id        = $row[0];
            $name      = $row[1];
            $password  = $row[2];
            $firstName = $row[3];
            $lastName  = $row[4];
            $wpUserId  = $row[5];
            $email     = $row[6];
            $user->setId((int) $id);
            $user->setName($name);
            $user->setPassword($password);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            if (null !== $wpUserId) {
                $wpUserId = (int) $wpUserId;
            }
            $user->setWpUserId($wpUserId);
            $user->setEmail($email);
            $roles = $this->getRolesByUser((int) $id);
            $user->setRoles($roles);
        }
        return $user;
    }

    public function getRolesByUser(int $id): ?BinarySearchTree {
        $tree      = null;
        $sql       = "select r.id from role r 
                left join user_role ur on r.id = ur.role_id
                where ur.user_id = :user_id";
        $statement = $this->connector->prepare($sql);
        $statement->bindParam(":user_id", $id, PDO::PARAM_INT);
        $statement->execute();

        $tree = new BinarySearchTree();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id = $row[0];
            $tree->insertValue($id);
        }
        return $tree;
    }

    public function update(User $user): bool {
        $sql       = "
                        update user set 
                                      first_name  = :first_name
                                    , last_name   = :last_name
                                    , updated_at  = :updated_at
                                    , wp_user_id  = :wp_user_id
                                    , password    = :password
                        where id = :id;";
        $statement = $this->connector->prepare($sql);

        $firstName = $user->getFirstName();
        $lastName  = $user->getLastName();
        $updatedAt = DateTimeUtil::getUnixTimestamp();
        $wpUserId  = $user->getWpUserId();
        $id        = $user->getId();
        $password  = $user->getPassword();

        $statement->bindParam("first_name", $firstName);
        $statement->bindParam("last_name", $lastName);
        $statement->bindParam("updated_at", $updatedAt);
        $statement->bindParam("wp_user_id", $wpUserId);
        $statement->bindParam("id", $id);
        $statement->bindParam("password", $password);
        $statement->execute();
        $roles = $this->roleService->toArray(
        /** @phpstan-ignore-next-line */
            $user->getRoles()
        );
        $this->updateUserRoles($user->getId(), $roles);
        Didapptic::getServer()->clearCaches();
        return $statement->rowCount() > 0;
    }

    public function updateUserRoles(int $userId, array $roles): void {
        $sql       = "DELETE FROM `user_role` WHERE `user_id` = :user_id;";
        $statement = $this->connector->prepare($sql);
        $createTs  = DateTimeUtil::getUnixTimestamp();
        $statement->bindParam("user_id", $userId);
        $statement->execute();

        foreach ($roles as $role) {
            $sql       = "INSERT INTO `user_role` (`role_id`, `user_id`, `create_ts`) VALUES (:role_id, :user_id, :create_ts);";
            $statement = $this->connector->prepare($sql);
            $statement->bindParam("role_id", $role);
            $statement->bindParam("user_id", $userId);
            $statement->bindParam("create_ts", $createTs);
            $statement->execute();
        }
    }

    public function getUser(string $userName): ?User {
        $sql       = "select id
                      , name
                      , password
                      , email
                from user 
                  where name = :name
                  or email = :name
                and isnull(register_token)
                and isnull(confirmation_token);";
        $statement = $this->connector->prepare($sql);
        $statement->bindParam(":name", $userName);

        $statement->execute();
        if ($statement->rowCount() === 0) {
            return null;
        }
        $user = new User();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id       = $row[0];
            $name     = $row[1];
            $password = $row[2];
            $email    = $row[3];
            $user->setId((int) $id);
            $user->setName($name);
            $user->setPassword($password);
            $roles = $this->getRolesByUser(intval($id));
            $user->setRoles($roles);
            $user->setEmail($email);
        }

        return $user;
    }

    public function getAll(): ArrayList {
        $userList = new ArrayList();
        $sql      = "SELECT 
                            u.`id`
                            , u.`first_name`
                            , u.`last_name`
                            , u.`name`
                            , u.`email`
                            , u.`password`
                            , u.`website_url`
                            , u.`newsletter`
                            , u.`register_token`
                            , u.`confirmation_token`
                            , u.`created_at`
                            , u.`updated_at`
                            , u.`wp_user_id`
                FROM `user` u;";

        $statement = $this->connector->prepare($sql);

        $statement->execute();
        if ($statement->rowCount() === 0) return $userList;

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $user              = new User();
            $id                = $row[0];
            $firstName         = $row[1];
            $lastName          = $row[2];
            $name              = $row[3];
            $email             = $row[4];
            $password          = $row[5];
            $websiteUrl        = $row[6];
            $newsletter        = $row[7];
            $registerToken     = $row[8];
            $confirmationToken = $row[9];
            $createTs          = $row[10];
            $updateTs          = $row[11];
            $wpUserId          = $row[12];

            if (null !== $wpUserId) {
                $wpUserId = (int) $wpUserId;
            }

            $user->setId((int) $id);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setWebsiteURL($websiteUrl);
            $user->setNewsletter("1" === $newsletter);
            $user->setRegisterToken($registerToken);
            $user->setConfirmationToken($confirmationToken);
            $dateTime = null;
            if (null !== $createTs) {
                $dateTime = new DateTime();
                $dateTime->setTimestamp((int) $createTs);
            }
            $user->setCreateTs($dateTime);
            $dateTime = null;
            if (null !== $updateTs) {
                $dateTime = new DateTime();
                $dateTime->setTimestamp((int) $updateTs);
            }
            $user->setUpdateTs($dateTime);
            $user->setWpUserId($wpUserId);
            $roles = $this->getRolesByUser((int) $id);
            $user->setRoles($roles);
            $userList->add($user);
        }
        return $userList;
    }

}
