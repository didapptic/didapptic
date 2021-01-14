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
use Didapptic\Object\Constant\Backend\Notification\Notification;
use Didapptic\Object\Constant\Backend\Notification\Notification as NotificationConstants;
use Didapptic\Object\Constant\Backend\Notification\Type;
use Didapptic\Object\Constant\Backend\Notification\Type as TypeConstants;
use Didapptic\Object\Environment;
use Didapptic\Object\Permission;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Notification\NotificationService;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Template\TemplateRenderer;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\UserService;
use doganoo\INotify\Notification\Type\IType;
use Exception;

/**
 * Class PasswordLostSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class PasswordLostSubmit extends AbstractSubmit {

    /** @var Environment */
    private $environment;
    /** @var UserService */
    private $userService;
    /** @var NotificationService */
    private $notificationService;
    /** @var TemplateRenderer */
    private $templateRenderer;
    /** @var PermissionService */
    private $permissionService;
    /** @var UserRepository */
    private $userManager;
    /** @var ReceiverService */
    private $receiverService;

    public function __construct(
        Environment $environment
        , UserService $userService
        , NotificationService $notificationService
        , TemplateRenderer $templateRenderer
        , PermissionService $permissionService
        , UserRepository $userManager
        , ReceiverService $receiverService
    ) {
        parent::__construct();
        $this->environment         = $environment;
        $this->userService         = $userService;
        $this->notificationService = $notificationService;
        $this->templateRenderer    = $templateRenderer;
        $this->permissionService   = $permissionService;
        $this->userManager         = $userManager;
        $this->receiverService     = $receiverService;
    }

    protected function valid(): bool {

        $valid = null !== $this->getArgument("username")
            && "" !== $this->getArgument("username");

        if (!$valid) return false;
        return true;
    }

    protected function onCreate(): void {

    }

    /**
     * @return bool
     */
    protected function create(): bool {
        $username = $this->getArgument("username");

        if (null === $username) {
            throw new Exception('missing username');
        }

        $response = $this->userService->reset($username);

        if (null === $response) return false;

        $token  = $response["token"];
        $userId = $response["user_id"];

        if ("" === $token || "" === $userId) return false;

        $user = $this->userManager->getUserById((int) $userId);

        if (null === $user) return false;

        $content = $this->templateRenderer->loadTemplate(
            "registration_email"
            , [
                "detailText"     => "Falls Sie diese Anfrage gesendet haben, klicken Sie auf den unteren Button. Andernfalls k&ouml;nnen Sie diese E-Mail einfach ignorieren."
                , "confirmText"  => "zur Plattform"
                , "confirmRoute" => "/menu/password-lost/$token/"
                , "baseURL"      => Didapptic::getBaseURL(true)
                , "appName"      => Didapptic::APP_NAME
                , "devText"      => "Diese E-Mail stammt aus unserem Test-System. Bitte benachrichten Sie uns umgehend wenn Sie diese E-Mail versehentlich erhalten haben."
            ]
        );

        $notification = new \doganoo\IN\Notification\Notification();
        $notification->setId(NotificationConstants::PASSWORD_LOST);
        $notification->setContent($content);
        $notification->setDelay(0);
        $notification->setSubject("Ihre Registrierung auf didapptic.com");
        $notification->setExecuted(false);
        $type = new \doganoo\IN\Notification\Type\Type();
        $type->setId(TypeConstants::NOTIFICATION[NotificationConstants::PASSWORD_LOST][IType::MAIL] ?? -1);
        $type->setPermission(
            $this->permissionService->toPermission(Permission::DEFAULT_PERMISSION)
        );
        $type->setName(IType::MAIL);
        $type->setCreateTs(new DateTime());
        $type->setMandatory(true);
        $notification->addType($type);
        $notification->setCreateTs(new DateTime());
        $notification->addReceiver(
            $this->receiverService->toReceiver($user)
        );

        return $this->notificationService->addToQueue($notification);
    }

    protected function onDestroy(): void {

    }

}
