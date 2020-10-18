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
use Didapptic\Object\User;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Notification\Participant\SenderService;
use Didapptic\Service\User\Permission\PermissionService;
use doganoo\IN\Notification\Notification;
use doganoo\IN\Notification\Type\Type;
use doganoo\IN\Notification\Type\TypeList;
use doganoo\IN\Participant\ReceiverList;
use doganoo\IN\Queue\Notification as QueueNotification;
use doganoo\IN\Queue\Queue;
use doganoo\IN\Queue\Type as QueueType;
use doganoo\INotify\Notification\INotification;
use doganoo\INotify\Notification\Type\IType;
use doganoo\INotify\Notification\Type\ITypeList;
use doganoo\INotify\Participant\IReceiver;
use doganoo\INotify\Participant\IReceiverList;
use doganoo\INotify\Queue\INotification as IQueueNotification;
use doganoo\INotify\Queue\IQueue;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Service\DateTime\DateTimeService;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

/**
 * Class NotificationManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class NotificationRepository {

    /** @var PDOConnector */
    private $connector;
    /** @var UserRepository */
    private $userManager;
    /** @var ReceiverService */
    private $receiverService;
    /** @var SenderService */
    private $senderService;
    /** @var PermissionService */
    private $permissionService;
    /** @var DateTimeService */
    private $dateTimeService;

    public function __construct(
        PDOConnector $connector
        , UserRepository $userManager
        , ReceiverService $receiverService
        , SenderService $senderService
        , PermissionService $permissionService
        , DateTimeService $dateTimeService
    ) {
        $this->connector = $connector;
        $this->connector->connect();
        $this->userManager       = $userManager;
        $this->receiverService   = $receiverService;
        $this->senderService     = $senderService;
        $this->permissionService = $permissionService;
        $this->dateTimeService   = $dateTimeService;
    }

    public function getNotificationQueue(bool $includeExecuted = false): IQueue {
        $queue = new Queue();

        $sql = "select 
                        nq.`id`
                        , nq.`content`
                        , nq.`subject`
                        , nq.`notification_type`
                        , nq.`executed`
                        , nq.`user_data`
                        , nq.`delay`
                        , nq.`notification_id`
                        , nq.`create_ts`
                from `notification_queue` nq
                    where nq.`executed` IN (:executedFirst, :executedSecond)
;";

        $executedFirst  = "false";
        $executedSecond = "false";
        if (true === $includeExecuted) {
            $executedSecond = "true";
        }

        $connection = $this->connector->getConnection();
        $statement  = $connection->prepare($sql);

        $statement->bindParam("executedFirst", $executedFirst);
        $statement->bindParam("executedSecond", $executedSecond);

        $statement->execute();
        if ($statement->rowCount() === 0) return $queue;

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $queueId            = $row[0];
            $content            = $row[1];
            $subject            = $row[2];
            $notificationTypeId = (int) $row[3];
            $executed           = $row[4];
            $userData           = json_decode($row[5], true);
            $delay              = (int) $row[6];
            $notificationId     = (int) $row[7];
            $createTs           = (int) $row[8];

            $notification = new QueueNotification();
            $notification->setId((int) $queueId);
            $notification->setContent($content);
            $notification->setSubject($subject);
            $notification->setExecuted("true" === $executed);
            $notification->setDelay($delay);
            $dateTime = new DateTime();
            $dateTime->setTimestamp($createTs);
            $notification->setCreateTs($dateTime);
            $notification->setSender(
                $this->senderService->getSystemSender()
            );

            $queueType        = new QueueType();
            $notificationType = $this->getType(
                $notificationTypeId
            );
            $queueType->setId($notificationType->getId());
            $queueType->setName($notificationType->getName());
            $queueType->setMandatory($notificationType->isMandatory());
            $queueType->setCreateTs($notificationType->getCreateTs());
            $queueType->setPermission($notificationType->getPermission());
            $notification->setType($queueType);

            $notification->addReceiver(
                $this->receiverService->toReceiverArray($userData)
            );
            $notification->setNotificationId($notificationId);
            $queue->add($notification);
        }

        return $queue;
    }

    private function getType(int $id): ?Type {
        $type = new Type();

        $sql = "select 
                        nt.`id`             # 0
                        , nt.`name`         # 1
                        , nt.`mandatory`    # 2
                        , nt.`create_ts`    # 3
                        , nt.`permission`   # 4   
                from `notification_type` nt
                    where nt.`id` = :id
;";

        $connection = $this->connector->getConnection();
        $statement  = $connection->prepare($sql);

        $statement->bindParam("id", $id);

        $statement->execute();
        if ($statement->rowCount() === 0) return null;

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id         = (int) $row[0];
            $name       = $row[1];
            $mandatory  = "1" === $row[2];
            $createTs   = (int) $row[3];
            $permission = (int) $row[4];

            $receiverList = $this->getReceiverListForType((int) $id);

            $type->setId($id);
            $type->setName($name);
            $type->setMandatory($mandatory);
            $type->setCreateTs(
                $this->dateTimeService->fromTimestamp(
                    $createTs
                )
            );
            $type->setReceiverList($receiverList);
            $type->setPermission(
                $this->permissionService->toPermission($permission)
            );

        }
        return $type;

    }

    private function getReceiverListForType(int $typeId): IReceiverList {
        $list = new ReceiverList();

        $sql = "select 
                        ntu.`id`
                        , ntu.`user_id`
                        , ntu.`active`
                from `notification_type_user` ntu
                    where ntu.`notification_type_id` = :n_id
;";

        $connection = $this->connector->getConnection();
        $statement  = $connection->prepare($sql);

        $statement->bindParam("n_id", $typeId);

        $statement->execute();
        if ($statement->rowCount() === 0) return $list;

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id     = (int) $row[0];
            $userId = (int) $row[1];
            $active = (int) $row[2];

            $active         = 1 === $active;
            $registeredUser = $this->userManager->getUserById((int) $row[1]);

            if (false === $active) continue;
            if (null === $registeredUser) continue;

            $list->add(
                $this->receiverService->toReceiver($registeredUser)
            );
        }
        return $list;
    }

    public function getAll(): ?ArrayList {
        $list = new ArrayList();

        $sql = "select 
                        n.`id`
                        , n.`name`
                        , n.`create_ts` 
                from `notification` n;";

        $connection = $this->connector->getConnection();
        $statement  = $connection->prepare($sql);

        $statement->execute();
        if ($statement->rowCount() === 0) return null;

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id       = $row[0];
            $name     = $row[1];
            $createTs = $row[2];

            $types = $this->getTypesPerNotification($id);

            $notification = new Notification();
            $notification->setId((int) $id);
            $notification->setName($name);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $createTs);
            $notification->setCreateTs($dateTime);
            $notification->setTypes($types);

            $list->add($notification);
        }

        return $list;
    }

    private function getTypesPerNotification($id): ?ITypeList {
        $list = new TypeList();

        $sql = "select 
                        nt.`id`
                        , nt.`name`
                        , nt.`mandatory`
                        , nt.`create_ts`
                        , nt.`permission`
                from `notification_type` nt
                    where nt.`notification_id` = :n_id
;";

        $connection = $this->connector->getConnection();
        $statement  = $connection->prepare($sql);

        $statement->bindParam("n_id", $id);

        $statement->execute();
        if ($statement->rowCount() === 0) return null;

        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id           = (int) $row[0];
            $name         = $row[1];
            $mandatory    = (bool) $row[2];
            $createTs     = (int) $row[3];
            $permission   = (int) $row[4];
            $receiverList = $this->getReceiverListForType((int) $id);

            $type = new Type();
            $type->setId((int) $id);
            $type->setName($name);
            $type->setMandatory((bool) $mandatory);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $createTs);
            $type->setCreateTs($dateTime);
            $type->setReceiverList($receiverList);
            $type->setPermission(
                $this->permissionService->toPermission($permission)
            );

            $list->add($type);
        }
        return $list;

    }

    public function addToQueue(INotification $notification): bool {

        $sql       = "INSERT INTO `notification_queue` (
                                     `content`
                                    , `subject`
                                    , `notification_type`
                                    , `executed`
                                    , `user_data`
                                    , `delay`
                                    , `notification_id`
                                    , `create_ts`
                                  ) VALUES (
                                    :content
                                    , :subject
                                    , :notification_type
                                    , :executed
                                    , :user_data
                                    , :delay
                                    , :notification_id
                                    , :create_ts
                                    )";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }

        $content        = $notification->getContent();
        $createTs       = $notification->getCreateTs();
        $createTs       = $createTs->getTimestamp();
        $delay          = $notification->getDelay();
        $executed       = $notification->isExecuted() ? 'true' : 'false';
        $subject        = $notification->getSubject();
        $notificationId = $notification->getId();
        /** @var ITypeList|ArrayList $types */
        $types = $notification->getTypes();
        /** @var IType $type */
        $type             = $types->get(0);
        $notificationType = $type->getId();
        /** @var IReceiverList|ArrayList $receiverList */
        $receiverList = $notification->getReceiverList();
        /** @var IReceiver $receiver */
        $receiver = $receiverList->get(0);
        $userData = json_encode(
            [
                "id"      => $receiver->getId()
                , "name"  => $receiver->getDisplayName()
                , "email" => $receiver->getEmail()
            ]
        );

        $statement->bindParam(":content", $content);
        $statement->bindParam(":subject", $subject);
        $statement->bindParam(":notification_type", $notificationType);
        $statement->bindParam(":executed", $executed);
        $statement->bindParam(":user_data", $userData);
        $statement->bindParam(":delay", $delay);
        $statement->bindParam(":notification_id", $notificationId);
        $statement->bindParam(":create_ts", $createTs);

        return $statement->execute();

    }

    public function updateQueue(IQueue $queue): void {

        /** @var IQueueNotification $notification */
        foreach ($queue as $notification) {
            $this->updateQueueEntry($notification);
        }
    }

    private function updateQueueEntry(IQueueNotification $notification) {

        $sql       = "UPDATE `notification_queue` SET `executed` = :executed WHERE id = :id;";
        $statement = $this->connector->prepare($sql);

        $executed = true === $notification->isExecuted() ? "true" : "false";
        $id       = $notification->getId();

        $statement->bindParam(":executed", $executed);
        $statement->bindParam(":id", $id);

        return $statement->execute();

    }

    public function removeUserTypes(User $user): void {
        $sql       = "DELETE FROM `notification_type_user` WHERE `user_id` = :user_id;";
        $statement = $this->connector->prepare($sql);
        $userId    = $user->getId();
        $statement->bindParam("user_id", $userId);
        $statement->execute();
    }

    public function updateUserTypes(IType $type): void {
        /** @var IReceiver $receiver */
        foreach ($type->getReceiverList() as $receiver) {
            $sql                = "INSERT INTO `notification_type_user` (`notification_type_id`, `user_id`, `active`) VALUES (:nti, :user_id, :active);";
            $statement          = $this->connector->prepare($sql);
            $notificationTypeId = $type->getId();
            $userId             = $receiver->getId();
            $active             = 1;

            FileLogger::debug(json_encode([
                "nti"      => $notificationTypeId
                , "userId" => $userId
                , "active" => $active
            ]));

            $statement->bindParam("nti", $notificationTypeId);
            $statement->bindParam("user_id", $userId);
            $statement->bindParam("active", $active);
            $statement->execute();
        }
    }

}
