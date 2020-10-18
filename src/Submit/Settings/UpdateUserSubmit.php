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

namespace Didapptic\Submit\Settings;

use Didapptic\Didapptic;
use Didapptic\Object\Permission;
use Didapptic\Object\User;
use Didapptic\Repository\NotificationRepository;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Notification\NotificationService;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\UserService;
use Didapptic\Submit\AbstractSubmit;
use doganoo\IN\Participant\NotificationList;
use doganoo\INotify\Notification\INotification;
use doganoo\INotify\Notification\INotificationList;
use doganoo\INotify\Notification\Type\IType;

/**
 * Class UpdateUserSubmit
 *
 * @package Didapptic\Submit\Settings
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class UpdateUserSubmit extends AbstractSubmit {

    private const RETURN_CODE_VALID         = 0;
    private const RETURN_CODE_NAME_EXISTS   = 1;
    private const RETURN_CODE_WEAK_PASSWORD = 2;
    private const RETURN_CODE_DB_ERROR      = 3;

    /** @var UserService */
    private $userService;
    /** @var UserRepository */
    private $userRepository;
    /** @var PermissionService */
    private $permissionService;
    /** @var NotificationService */
    private $notificationService;
    /** @var NotificationRepository */
    private $notificationRepository;

    public function __construct(
        UserService $userService
        , UserRepository $userRepository
        , PermissionService $permissionService
        , NotificationService $notificationService
        , NotificationRepository $notificationRepository
    ) {
        parent::__construct();
        $this->userService            = $userService;
        $this->userRepository         = $userRepository;
        $this->permissionService      = $permissionService;
        $this->notificationService    = $notificationService;
        $this->notificationRepository = $notificationRepository;
    }

    protected function valid(): bool {
        $id      = (int) $this->getArgument("id");
        $user    = $this->userService->getUser();
        $isAdmin = $this->permissionService->hasPermission(
            $this->permissionService->toPermission(Permission::MENU_SETTINGS_ADMIN)
        );

        if (true === $isAdmin) return true;
        if (null === $user) return false;
        if ($id !== $user->getId()) return false;
        return true;
    }

    protected function onCreate(): void {
        $this->addResponse(
            "return_code"
            , UpdateUserSubmit::RETURN_CODE_VALID
        );
    }

    protected function create(): bool {
        $arguments = $this->getArguments();
        $user      = $this->userService->toUser($arguments);

        $cacheUsers = Didapptic::getServer()->getUsersFromCache();

        $nameExists = false;
        foreach ($cacheUsers->keySet() as $key) {
            /** @var User $cacheUser */
            $cacheUser = $cacheUsers->get($key);

            if (
                $cacheUser->getName() === $user->getName()
                && $cacheUser->getId() !== $user->getId()
            ) {
                $nameExists = true;
                break;
            }
        }

        if (true === $nameExists) {
            $this->addResponse(
                "return_code"
                , UpdateUserSubmit::RETURN_CODE_NAME_EXISTS
            );
            return false;
        }

        $isStrongPassword = $this->userService->isStrongPassword($user->getPassword());


        if (false === $isStrongPassword) {
            $this->addResponse(
                "return_code"
                , UpdateUserSubmit::RETURN_CODE_WEAK_PASSWORD
            );
            return false;
        }

        $updated = $this->userRepository->update($user);

        if (false === $updated) {
            $this->addResponse(
                "return_code"
                , UpdateUserSubmit::RETURN_CODE_DB_ERROR
            );
            return false;
        }

        /** @var INotificationList|NotificationList $notificationList */
        $notificationList = $this->notificationService->toNotificationUser($user, (array) $arguments["notifications"]);
        $this->notificationRepository->removeUserTypes($user);
        /** @var INotification $notification */
        foreach ($notificationList as $notification) {
            /** @var IType $type */
            foreach ($notification->getTypes() as $type) {
                $this->notificationRepository->updateUserTypes($type);
            }
        }
        return $updated;
    }

    protected function onDestroy(): void {

    }

}
