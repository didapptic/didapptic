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

namespace Didapptic\Service\Notification;

use Didapptic\Object\Environment;
use Didapptic\Object\User;
use Didapptic\Repository\NotificationRepository;
use Didapptic\Service\Notification\Config\Email\EmailService;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Notification\Participant\SenderService;
use Didapptic\Service\User\UserService;
use doganoo\IN\Notification\Notification;
use doganoo\IN\Notification\NotificationList;
use doganoo\IN\Notification\Type\Type;
use doganoo\INotify\Notification\INotification;
use doganoo\INotify\Notification\INotificationList;

/**
 * Class NotificationService
 *
 * @package Didapptic\Service\Notification
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class NotificationService {

    /** @var NotificationRepository */
    private $notificationManager;
    /** @var UserService */
    private $userService;
    /** @var EmailService */
    private $emailService;
    /** @var SenderService */
    private $senderService;
    /** @var Environment */
    private $environment;
    /** @var ReceiverService */
    private $receiverService;

    public function __construct(
        NotificationRepository $notificationManager
        , UserService $userService
        , EmailService $emailService
        , SenderService $senderService
        , Environment $environment
        , ReceiverService $receiverService
    ) {
        $this->notificationManager = $notificationManager;
        $this->userService         = $userService;
        $this->emailService        = $emailService;
        $this->senderService       = $senderService;
        $this->environment         = $environment;
        $this->receiverService     = $receiverService;
    }

    public function addToQueue(INotification $notification): bool {
        return $this->notificationManager->addToQueue($notification);
    }

    public function toNotificationUser(User $user, array $userNotification): INotificationList {
        $notificationList = new NotificationList();
        /**
         * @var string $notificationId
         * @var array  $types
         */
        foreach ($userNotification as $notificationId => $types) {
            $notification = new Notification();
            $notification->setId((int) $notificationId);

            foreach ($types as $typeId) {
                $type = new Type();
                $type->setId((int) $typeId);
                $type->addReceiver(
                    $this->receiverService->toReceiver($user)
                );
                $notification->addType($type);
            }

            $notificationList->add($notification);
        }

        return $notificationList;
    }

}
