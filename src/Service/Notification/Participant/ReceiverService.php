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

namespace Didapptic\Service\Notification\Participant;

use Didapptic\Didapptic;
use Didapptic\Object\Environment;
use Didapptic\Object\User;
use doganoo\IN\Participant\Receiver;
use doganoo\IN\Participant\ReceiverList;
use doganoo\INotify\Participant\IReceiver;
use doganoo\INotify\Participant\IReceiverList;

/**
 * Class ReceiverService
 *
 * @package Didapptic\Service\Notification\Participant
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class ReceiverService {

    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment) {
        $this->environment = $environment;
    }

    public function toReceiverList(User $user): IReceiverList {
        $list     = new ReceiverList();
        $receiver = $this->toReceiver($user);
        $list->add($receiver);
        return $list;
    }

    public function toReceiver(User $user): IReceiver {
        $receiver = new Receiver();
        $receiver->setId($user->getId());
        $receiver->setEmail($user->getEmail());
        $receiver->setDisplayname($user->getName());
        return $receiver;
    }

    public function getDefaultReceiver(): IReceiver {
        $receiver = new Receiver();
        $receiver->setId(Didapptic::APP_USER_ID);
        $receiver->setDisplayname($this->environment->read("email.default.receiver.name"));
        $receiver->setEmail($this->environment->read("email.default.receiver.address"));
        return $receiver;
    }

    /**
     * This method is important for persons who should get notified and are not
     * registered on didapptic now
     *
     * @param array $user
     *
     * @return IReceiver
     */
    public function toReceiverArray(array $user): IReceiver {
        if (true === $this->environment->isDebug()) return $this->getDefaultReceiver();
        $receiver = new Receiver();
        $receiver->setId($user["id"]);
        $receiver->setEmail($user["email"]);
        $receiver->setDisplayname($user["name"]);
        return $receiver;
    }

}
