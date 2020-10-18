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

use Didapptic\Didapptic;
use Didapptic\Exception\DidappticException;
use doganoo\IN\Handler\Applicant\Log\LogApplicant;
use doganoo\IN\Handler\Applicant\Mail\MailApplicant;
use doganoo\IN\Handler\Applicant\Mail\PlainMailApplicant;
use doganoo\INotify\Handler\Applicant\IApplicant;
use doganoo\INotify\Notification\Type\IType as INotificationType;
use doganoo\INotify\Queue\IType as IQueueType;
use doganoo\INotify\Service\Mapper\IMapper;

/**
 * Class Mapper
 *
 * @package Didapptic\Service\Notification
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Mapper implements IMapper {

    private $config = [
        INotificationType::MAIL         => MailApplicant::class
        , INotificationType::PLAIN_MAIL => PlainMailApplicant::class
        , INotificationType::LOG        => LogApplicant::class
    ];

    /**
     * @param IQueueType $type
     *
     * @return IApplicant
     * @throws DidappticException
     */
    public function query(IQueueType $type): IApplicant {
        $serviceName = $this->config[$type->getName()] ?? null;

        if (null === $serviceName) {
            throw new DidappticException();
        }

        return Didapptic::getServer()->query($serviceName);
    }

}
