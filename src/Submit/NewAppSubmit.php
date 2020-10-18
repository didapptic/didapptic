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
use Didapptic\Service\App\Submit\AppSubmitService;
use Didapptic\Service\HTTP\URL\URLService;
use Didapptic\Service\Notification\NotificationService;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Template\TemplateRenderer;
use Didapptic\Service\User\Permission\PermissionService;
use doganoo\IN\Notification\Notification;
use doganoo\IN\Notification\Type\Type;
use doganoo\INotify\Notification\Type\IType;
use doganoo\PHPUtil\Log\FileLogger;
use function array_merge;

/**
 * Class NewAppSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class NewAppSubmit extends AbstractSubmit {

    private $parameters = [
        "google" => [
            "id"            => null
            , "presentable" => false
        ]
        , "ios"  => [
            "id"            => null
            , "presentable" => false
        ]
    ];
    private $executed   = false;
    /** @var NotificationService */
    private $notificationService;
    /** @var AppSubmitService */
    private $appSubmitService;
    /** @var TemplateRenderer */
    private $templateRenderer;
    /** @var PermissionService */
    private $permissionService;
    /** @var ReceiverService */
    private $receiverService;
    /** @var URLService */
    private $urlService;

    public function __construct(
        NotificationService $notificationService
        , AppSubmitService $appSubmitService
        , TemplateRenderer $templateRenderer
        , PermissionService $permissionService
        , ReceiverService $receiverService
        , URLService $urlService
    ) {
        parent::__construct();

        $this->notificationService = $notificationService;
        $this->appSubmitService    = $appSubmitService;
        $this->templateRenderer    = $templateRenderer;
        $this->permissionService   = $permissionService;
        $this->receiverService     = $receiverService;
        $this->urlService          = $urlService;
    }

    protected function valid(): bool {

        $googleStoreUrl = $this->getArgument("google-store-url");
        $iosStoreUrl    = $this->getArgument("ios-store-url");

        $googleId     = $this->urlService->getParameterFromURL($googleStoreUrl, "id");
        $googleExists = null === $googleStoreUrl && null !== $googleId;

        $iosId     = $this->urlService->regexParameterFromURL($iosStoreUrl, "/id(\d+)/");
        $iosExists = null === $iosStoreUrl && null !== $iosId;

        $this->parameters["google"]["id"]          = $googleId;
        $this->parameters["google"]["presentable"] = $googleExists;

        $this->parameters["ios"]["id"]          = $iosId;
        $this->parameters["ios"]["presentable"] = $iosExists;

        return null !== $googleId || null !== $iosId;
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {
        $googleAdded = true;
        $iosAdded    = true;

        if (true === $this->parameters["ios"]["presentable"]) {
            $this->parameters["ios"]["operating-system"] = App::IOS;
            $iosStoreId                                  = $this->parameters["ios"]["id"];
            $iosAdded                                    = $this->appSubmitService->addApp(
                (string) $iosStoreId
                , array_merge($this->parameters["ios"], $this->getArguments())
                , App::IOS
            );
        }

        if (true === $this->parameters["google"]["presentable"]) {
            $this->parameters["google"]["operating-system"] = App::ANDROID;
            $googleStoreId                                  = $this->parameters["google"]["id"];
            $googleAdded                                    = $this->appSubmitService->addApp(
                (string) $googleStoreId
                , array_merge($this->parameters["google"], $this->getArguments())
                , App::ANDROID
            );
        }

        $this->executed =
            true === $iosAdded
            && true === $googleAdded;

        return $this->executed;
    }

    protected function onDestroy(): void {
        if (false === $this->executed) return;

        $content = $this->templateRenderer->loadTemplate(
            "default_template"
            , [
                "detailText"     => "Neue App(s) {$this->getAppIds()} wurde(n) eingetragen."
                , "confirmText"  => "zur Plattform"
                , "confirmRoute" => "/"
                , "baseURL"      => Didapptic::getBaseURL(true)
                , "appName"      => Didapptic::APP_NAME
                , "devText"      => "Diese E-Mail stammt aus unserem Test-System. Bitte benachrichten Sie uns umgehend wenn Sie diese E-Mail versehentlich erhalten haben."
            ]
        );

        $notification = new Notification();
        $notification->setId(NotificationConstants::NEW_APP_ADDED);
        $notification->setContent($content);
        $notification->setDelay(0);
        $notification->setSubject("Neue App hinzugef&uuml;gt | " . Didapptic::APP_NAME);
        $notification->setExecuted(false);
        $type = new Type();
        $type->setId(TypeConstants::NOTIFICATION[NotificationConstants::NEW_APP_ADDED][IType::MAIL] ?? -1);
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

        $this->notificationService->addToQueue($notification);

    }

    private function getAppIds() {
        $name = [];
        if (isset($this->parameters["ios"]["id"])) {
            $name[] = $this->parameters["ios"]["id"];
        }
        if (isset($this->parameters["google"]["id"])) {
            $name[] = $this->parameters["google"]["id"];
        }

        $names = implode(", ", $name);
        return $names;
    }

}
