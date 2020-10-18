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

namespace Didapptic\BackgroundJob\Task;

use Didapptic\Object\Environment;
use Didapptic\Repository\NotificationRepository;
use Didapptic\Repository\RequestRepository;
use Didapptic\Service\Notification\Participant\ReceiverService;
use doganoo\Backgrounder\Task\Task;
use doganoo\IN\Handler\NotificationHandler;
use doganoo\IN\Queue\Notification;
use doganoo\INotify\Participant\IReceiver;
use doganoo\INotify\Queue\INotification as IQueueNotification;
use doganoo\INotify\Queue\IQueue;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;

/**
 * for simplicity, we actually create this class for apps
 * that are deleted where creators should be notified.
 *
 * In the future, when the jobs accept arguments, we will
 * extend this class to be more generic.
 *
 * Class ActionNotifier
 *
 * @package BackgroundJob
 */
class ActionNotifier extends Task {

    /** @var NotificationHandler */
    private $notificationHandler;
    /** @var NotificationRepository */
    private $notificationManager;
    /** @var RequestRepository */
    private $requestManager;
    /** @var Environment */
    private $environment;
    /** @var ReceiverService */
    private $receiverService;

    public function __construct(
        NotificationRepository $notificationManager
        , RequestRepository $requestManager
        , NotificationHandler $notificationHandler
        , Environment $environment
        , ReceiverService $receiverService
    ) {

        $this->notificationManager = $notificationManager;
        $this->requestManager      = $requestManager;
        $this->notificationHandler = $notificationHandler;
        $this->environment         = $environment;
        $this->receiverService     = $receiverService;
    }

    protected function onAction(): void {

    }

    protected function action(): bool {

        FileLogger::debug("========= start action notifier =========");

        /** @var IQueue|ArrayList $queue */
        $queue = $this->notificationManager->getNotificationQueue();
        $queue = $this->replaceDebugNotification($queue);
        $this->notificationHandler->setNotifications($queue);
        $this->notificationHandler->notify();
        $this->notificationManager->updateQueue(
            $this->notificationHandler->getNotifications()
        );

        /** @var IQueueNotification $notification */
        foreach ($queue as $notification) {
            if (false === $notification->isExecuted()) continue;
            /** @var IReceiver $receiver */
            foreach ($notification->getReceiverList() as $receiver) {

                $this->requestManager->insert(
                    $receiver->getEmail()
                    , $receiver->getDisplayName()
                    , $notification->getContent()
                );

            }
        }

        FileLogger::debug("========= end action notifier =========");
        return true;
    }

    /**
     * @param IQueue $queue
     *
     * @return IQueue
     */
    private function replaceDebugNotification(IQueue $queue): IQueue {
        if (false === $this->environment->isDebug()) return $queue;

        /** @var IQueueNotification|Notification $notification */
        foreach ($queue as $notification) {

            /** @phpstan-ignore-next-line */
            $notification->getReceiverList()->clear();

            /** @phpstan-ignore-next-line */
            $notification->addReceiver(
                $this->receiverService->getDefaultReceiver()
            );

        }

        return $queue;
    }

    protected function onClose(): void {

    }

}
