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

namespace Didapptic\Submit;

use Didapptic\Repository\TokenRepository;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Application\WordPress\WordPressService;
use Didapptic\Service\User\UserService;
use doganoo\PHPUtil\Log\FileLogger;
use Exception;

/**
 * Class PasswordUpdateSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class PasswordUpdateSubmit extends AbstractSubmit {

    private const RETURN_CODE_OK                  = 0;
    private const RETURN_CODE_NO_USER_FOUND       = 1;
    private const RETURN_CODE_USER_NOT_UPDATED    = 2;
    private const RETURN_CODE_USER_USER_HAS_NO_WP = 3;
    private const RETURN_CODE_WP_NOT_UPDATED      = 4;

    /** @var WordPressService */
    private $wordPressService = null;
    /** @var UserRepository */
    private $userManager;
    /** @var TokenRepository */
    private $tokenManager;
    /** @var UserService */
    private $userService;

    public function __construct(
        WordPressService $wordPressService
        , UserRepository $userManager
        , TokenRepository $tokenManager
        , UserService $userService
    ) {
        parent::__construct();
        $this->wordPressService = $wordPressService;
        $this->userManager      = $userManager;
        $this->tokenManager     = $tokenManager;
        $this->userService      = $userService;
    }

    protected function valid(): bool {
        $arguments = $this->getArguments();
        return isset($arguments["password"])
            && isset($arguments["token"])
            && isset($arguments["user"]);
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {
        $password = $this->getArgument("password");
        $userId   = $this->getArgument("user");
        $token    = $this->getArgument("token");

        if (null === $password) {
            throw new Exception('password missing');
        }
        if (null === $userId) {
            throw new Exception('userid missing');
        }
        if (null === $token) {
            throw new Exception('token missing');
        }

        $hashedPassword = $this->userService->hashPassword($password);


        $user = $this->userManager->getUserById((int) $userId);

        $this->addResponse(
            "return_code"
            , PasswordUpdateSubmit::RETURN_CODE_OK
        );

        if (null === $user) {
            $this->addResponse(
                "return_code"
                , PasswordUpdateSubmit::RETURN_CODE_NO_USER_FOUND
            );
            return false;
        }

        $user->setPassword($hashedPassword);
        $user->setPasswordRepeat($hashedPassword);
        $user->setPlainPassword($password);
        $user->setPlainPasswordRepeat($password);

        $userUpdated = $this->userManager->update($user);

        if (false === $userUpdated) {
            $this->addResponse(
                "return_code"
                , PasswordUpdateSubmit::RETURN_CODE_USER_NOT_UPDATED
            );
            return false;
        }

        $this->tokenManager->deactivate($token);

        $wpId = $user->getWpUserId();

        if (null === $wpId) {
            $this->addResponse(
                "return_code"
                , PasswordUpdateSubmit::RETURN_CODE_USER_USER_HAS_NO_WP
            );
        } else {
            // The user is enabled for WordPress.
            // We try to update his password!
            $updated = $this->wordPressService->updateUser($wpId, $password);

            if (false === $updated) {
                $this->addResponse(
                    "return_code"
                    , PasswordUpdateSubmit::RETURN_CODE_WP_NOT_UPDATED
                );
                FileLogger::fatal("user could not be updated for wordpress! However, his didapptic password is updated!");
                return false;
            }

        }

        return true;

    }

    protected
    function onDestroy(): void {

    }

}
