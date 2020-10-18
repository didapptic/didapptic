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

use DateTime;
use Didapptic\Didapptic;
use Didapptic\Object\Constant\Backend\Notification\Notification as NotificationConstants;
use Didapptic\Object\Constant\Backend\Notification\Type as TypeConstants;
use Didapptic\Object\Permission;
use Didapptic\Service\Notification\NotificationService;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Template\TemplateRenderer;
use Didapptic\Service\User\Permission\PermissionService;
use doganoo\IN\Notification\Notification;
use doganoo\IN\Notification\Type\Type;
use doganoo\INotify\Notification\Type\IType;
use doganoo\PHPUtil\Log\FileLogger;

/**
 * Class NewContactSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class NewContactSubmit extends AbstractSubmit {

    /** @var NotificationService */
    private $notificationService;
    /** @var PermissionService */
    private $permissionService;
    /** @var ReceiverService */
    private $receiverService;
    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(
        NotificationService $notificationService
        , PermissionService $permissionService
        , ReceiverService $receiverService
        , TemplateRenderer $templateRenderer
    ) {
        parent::__construct();
        $this->notificationService = $notificationService;
        $this->permissionService   = $permissionService;
        $this->receiverService     = $receiverService;
        $this->templateRenderer    = $templateRenderer;
    }

    protected function onCreate(): void {

    }

    /**
     * @return bool
     */
    protected function create(): bool {
        $name    = $this->getArgument("name");
        $email   = $this->getArgument("email");
        $message = $this->getArgument("message");

        $content = $this->templateRenderer->loadTemplate(
            "default_template"
            , [
                "detailText"     => "Sie haben eine neue Anfrage von $name ($email): <br><br><br>$message"
                , "confirmText"  => "zur Plattform"
                , "confirmRoute" => "/"
                , "baseURL"      => Didapptic::getBaseURL(true)
                , "appName"      => Didapptic::APP_NAME
                , "devText"      => "Diese E-Mail stammt aus unserem Test-System. Bitte benachrichten Sie uns umgehend wenn Sie diese E-Mail versehentlich erhalten haben."
            ]
        );

        $notification = new Notification();
        $notification->setId(NotificationConstants::CONTACT_VIA_PLATFORM);
        $notification->setContent($content);
        $notification->setDelay(0);
        $notification->setSubject("neue Nachricht erhalten | " . Didapptic::APP_NAME);
        $notification->setExecuted(false);
        $type = new Type();
        $type->setId(TypeConstants::NOTIFICATION[NotificationConstants::CONTACT_VIA_PLATFORM][IType::MAIL] ?? -1);
        $type->setPermission(
            $this->permissionService->toPermission(Permission::DEFAULT_PERMISSION)
        );
        $type->setName(IType::MAIL);
        $type->setCreateTs(new DateTime());
        $type->setMandatory(true);
        $notification->addType($type);
        $notification->setCreateTs(new DateTime());
        $notification->addReceiver(
            $this->receiverService->getDefaultReceiver()
        );

        return $this->notificationService->addToQueue($notification);
    }

    protected function onDestroy(): void {

    }

    protected function valid(): bool {
        FileLogger::debug(json_encode($this->getArguments()));
        $name    = $this->getArgument("name");
        $email   = $this->getArgument("email");
        $message = $this->getArgument("message");

        if (null === $name || "" === $name) {
            return false;
        }
        if (null === $email || "" === $email) {
            return false;
        }
        if (null === $message || "" === $message) {
            return false;
        }
        return true;
    }

}
