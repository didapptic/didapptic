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

namespace Didapptic\Service\User;

use Didapptic\Didapptic;
use Didapptic\Object\User;
use Didapptic\Repository\TokenRepository;
use Didapptic\Repository\UserRepository;
use Didapptic\Server;
use Didapptic\Service\Session\SessionService;
use Didapptic\Service\User\Role\RoleService;
use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;
use doganoo\PHPUtil\Util\DateTimeUtil;
use doganoo\PHPUtil\Util\StringUtil;
use Exception;

/**
 * Class UserService
 *
 * @package Didapptic\Service
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class UserService {

    /** @var SessionService */
    private $sessionService;
    /** @var UserRepository */
    private $userManager;
    /** @var TokenRepository */
    private $tokenManager;
    /** @var RoleService */
    private $roleService;

    public function __construct(
        SessionService $sessionService
        , UserRepository $userManager
        , TokenRepository $tokenManager
        , RoleService $roleService
    ) {
        $this->sessionService = $sessionService;
        $this->userManager    = $userManager;
        $this->tokenManager   = $tokenManager;
        $this->roleService    = $roleService;
    }

    public function getUser(): ?User {
        $lastAccess    = $this->sessionService->get("last_access", null);
        $userId        = $this->sessionService->get("user_id", null);
        $beforeOneHour = DateTimeUtil::subtractHours(1);

        if (null === $lastAccess || null === $userId) {
            return null;
        }

        if ($beforeOneHour->getTimestamp() > (int) $lastAccess) {
            $this->sessionService->killAll();
            return null;
        }

        /** @var HashMap $users */
        $users = Didapptic::getServer()->query(Server::USER_HASH_MAP);
        $user  = $users->get((int) $userId);

        if (null === $user) {
            $this->sessionService->killAll();
            return null;
        }

        $this->sessionService->set("last_access", (string) DateTimeUtil::getUnixTimestamp());

        return $user;
    }

    /**
     * returns an user if the corresponding array indices are set,
     * otherwise null.
     *
     * @param array|null $array
     *
     * @return User|null
     */
    public function toUser(?array $array): ?User {

        if (
            null === $array
            || 0 === count($array)
        ) {
            return null;
        }

        $id         = $array["id"] ?? -1;
        $name       = $array["name"] ?? "";
        $firstName  = $array["first_name"] ?? "";
        $lastName   = $array["last_name"] ?? "";
        $email      = $array["email"] ?? "";
        $password   = $array["password"] ?? "";
        $website    = $array["website"] ?? "";
        $wpUserId   = $array["wp_user_id"] ?? null;
        $wpUserId   = true === is_numeric($wpUserId) ?
            (int) $wpUserId
            : null;
        $newsletter = "on" === ($array["newsletter"] ?? "");
        $roles      = $array["roles"] ?? [];
        $hashed     = $this->hashPassword($password);

        $user = new User();
        $user->setId((int) $id);
        $user->setName($name);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setPassword($hashed);
        $user->setPasswordRepeat($hashed);
        $user->setPlainPasswordRepeat($password);
        $user->setPlainPassword($password);
        $user->setWebsiteURL($website);
        $user->setWpUserId($wpUserId);
        $user->setNewsletter($newsletter);
        $user->setRoles(
            $this->roleService->toRoles($roles)
        );

        return $user;

    }

    /**
     * @param string $password
     *
     * @return string
     * @throws Exception
     */
    public function hashPassword(string $password): string {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        if (false === $hashed) {
            throw new Exception('could not hash password');
        }
        return $hashed;
    }

    public function reset(string $username): ?array {
        $uuid = StringUtil::getUUID();
        $user = $this->userManager->getUser($username);

        if (null === $user) {
            return null;
        }

        $inserted = $this->tokenManager->insert($uuid, $user->getId());

        if (false === $inserted) {
            return null;
        }

        return [
            "token"     => $uuid
            , "user_id" => $user->getId()
        ];

    }

    public function isStrongPassword(string $password): bool {
        $passwordLength = strlen($password);

        if (true === $passwordLength < 8) return false;

        // Check the number of upper case letters in the password
        if (strlen((string) preg_replace('/([^A-Z]*)/', '', $password)) < 1) return false;

        // Check the number of lower case letters in the password
        if (strlen((string) preg_replace('/([^a-z]*)/', '', $password)) < 1) return false;

        // minimum 1 number
        if (strlen((string) preg_replace('/([^0-9]*)/', '', $password)) < 1) return false;
        return true;
    }

}
