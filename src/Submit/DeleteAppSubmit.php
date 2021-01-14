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
use Didapptic\Object\App;
use Didapptic\Object\Constant\Backend\Notification\Notification as NotificationConstants;
use Didapptic\Object\Constant\Backend\Notification\Type as TypeConstants;
use Didapptic\Object\Permission;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Notification\NotificationService;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Template\TemplateRenderer;
use Didapptic\Service\User\Permission\PermissionService;
use doganoo\IN\Notification\Notification;
use doganoo\IN\Notification\Type\Type;
use doganoo\INotify\Notification\Type\IType;

/**
 * Class DeleteAppSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class DeleteAppSubmit extends AbstractSubmit {

    /** @var array */
    private $arguments;
    /** @var bool */
    private $deleted = false;
    /** @var App */
    private $app;
    /** @var AppRepository */
    private $appManager;
    /** @var NotificationService */
    private $notificationService;
    /** @var TemplateRenderer */
    private $templateRenderer;
    /** @var PermissionService */
    private $permissionService;
    /** @var ReceiverService */
    private $receiverService;
    /** @var UserRepository */
    private $userManager;

    public function __construct(
        AppRepository $appManager
        , NotificationService $notificationService
        , TemplateRenderer $templateRenderer
        , PermissionService $permissionService
        , ReceiverService $receiverService
        , UserRepository $userManager
    ) {
        parent::__construct();
        $this->appManager          = $appManager;
        $this->notificationService = $notificationService;
        $this->templateRenderer    = $templateRenderer;
        $this->permissionService   = $permissionService;
        $this->receiverService     = $receiverService;
        $this->userManager         = $userManager;
    }

    protected function valid(): bool {
        $this->arguments = $this->getArguments();
        $appId           = $this->arguments["appId"] ?? null;
        if (null === $appId) return false;
        if (false === is_numeric($appId)) return false;
        $app = $this->toApp((int) $appId);
        if (null === $app) return false;
        $this->app = $app;
        return true;
    }

    private function toApp(int $appId): ?App {
        $apps = Didapptic::getServer()->getAppsFromCache();

        /** @var App $app */
        foreach ($apps as $app) {
            if ($appId === $app->getId()) return $app;
        }

        return null;
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {
        $appId         = $this->arguments["appId"];
        $this->deleted = $this->appManager->hide($appId);
        return $this->deleted;
    }

    protected function onDestroy(): void {

        if (false === $this->deleted) return;

        if (null === $this->app) {
            return;
        }

        $content = $this->templateRenderer->loadTemplate(
            "default_template"
            , [
                "detailText"     => "Eine von Ihnen erstelle App mit dem Namen ({$this->app->getName()}) wurde entfernt!. <br><br><br>Sie erhalten diese E-Mail weil Sie diese App auf unserer Plattform erstellt haben."
                , "confirmText"  => "zur Plattform"
                , "confirmRoute" => "/"
                , "baseURL"      => Didapptic::getBaseURL(true)
                , "appName"      => Didapptic::APP_NAME
                , "devText"      => "Diese E-Mail stammt aus unserem Test-System. Bitte benachrichten Sie uns umgehend wenn Sie diese E-Mail versehentlich erhalten haben."
            ]
        );

        $notification = new Notification();
        $notification->setId(NotificationConstants::DELETED_APP);
        $notification->setContent($content);
        $notification->setDelay(0);
        $notification->setSubject("{$this->app->getName()} entfernt | " . Didapptic::APP_NAME);
        $notification->setExecuted(false);
        $type = new Type();
        $type->setId(TypeConstants::NOTIFICATION[NotificationConstants::DELETED_APP][IType::MAIL] ?? -1);
        $type->setPermission(
            $this->permissionService->toPermission(Permission::DEFAULT_PERMISSION)
        );
        $type->setName(IType::MAIL);
        $type->setCreateTs(new DateTime());
        $type->setMandatory(true);
        $notification->addType($type);
        $notification->setCreateTs(new DateTime());
        $notification->addReceiver(
            $this->receiverService->toReceiver(
            /** @phpstan-ignore-next-line */
                $this->userManager->getUserById(
                    $this->app->getAuthor()
                )
            )
        );

        $this->notificationService->addToQueue($notification);

    }

}

