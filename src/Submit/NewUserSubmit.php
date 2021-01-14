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
use Didapptic\Object\Environment;
use Didapptic\Object\Permission;
use Didapptic\Object\Registrant;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Application\WordPress\WordPressService;
use Didapptic\Service\Notification\NotificationService;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Template\TemplateRenderer;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\Register\UserRegisterService;
use Didapptic\Service\User\UserService;
use doganoo\IN\Notification\Notification;
use doganoo\IN\Notification\Type\Type;
use doganoo\INotify\Notification\Type\IType;
use doganoo\PHPUtil\Log\FileLogger;
use Exception;

class NewUserSubmit extends AbstractSubmit {

    /** Whether the user could be created on API side */
    private const REGISTER_TYPE_API_ERROR = 1;
    /** User created on API side, but not on WordPress */
    private const REGISTER_TYPE_WP_NOT_CREATED = 2;
    /** User created on API side and WordPress, but could not set WP flag */
    private const REGISTER_TYPE_WP_NOT_UPDATED = 3;
    /** User created on API side and WordPress */
    private const REGISTER_TYPE_USER_REGULARLY_CREATED = 4;

    /** @var Environment $environment */
    private $environment;
    /** @var null|Registrant $registrant */
    private $registrant = null;
    /** @var WordPressService $wordPressService */
    private $wordPressService = null;
    /** @var UserService $userService */
    private $userService = null;
    /** @var int $registerType */
    private $registerType;
    /** @var null|string $confirmationToken */
    private $confirmationToken = null;
    /** @var bool $registerImmediately */
    private $registerImmediately = true;
    /** @var UserRepository */
    private $userManager;
    /** @var NotificationService */
    private $notificationService;
    /** @var UserRegisterService */
    private $userRegisterService;
    /** @var TemplateRenderer */
    private $templateRenderer;
    /** @var PermissionService */
    private $permissionService;
    /** @var ReceiverService */
    private $receiverService;

    public function __construct(
        UserService $userService
        , Environment $environment
        , WordPressService $wordPressService
        , UserRepository $userManager
        , NotificationService $notificationService
        , UserRegisterService $userRegisterService
        , TemplateRenderer $templateRenderer
        , PermissionService $permissionService
        , ReceiverService $receiverService
    ) {
        parent::__construct();

        $this->environment         = $environment;
        $this->userService         = $userService;
        $this->wordPressService    = $wordPressService;
        $this->userManager         = $userManager;
        $this->notificationService = $notificationService;
        $this->userRegisterService = $userRegisterService;
        $this->templateRenderer    = $templateRenderer;
        $this->permissionService   = $permissionService;
        $this->receiverService     = $receiverService;
    }

    protected function valid(): bool {
        return true;
    }

    protected function onCreate(): void {
        $this->registrant = $this->userRegisterService->toRegistrant($this->getArguments());

        $this->registrant->setPassword(
            $this->userService->hashPassword($this->registrant->getPassword())
        );
        $this->registrant->setPasswordRepeat(
            $this->userService->hashPassword($this->registrant->getPasswordRepeat())
        );

    }

    protected function create(): bool {
        if (null === $this->registrant) {
            throw new Exception('no registrant');
        }
        $this->confirmationToken = $this->userRegisterService->generateToken();
        $this->registrant        = $this->userRegisterService->registerUser(
            $this->registrant
            , $this->confirmationToken
        );

        FileLogger::debug($this->registrant->getStatus());

        if (false === $this->registrant->isValid()) {
            $this->registerType = NewUserSubmit::REGISTER_TYPE_API_ERROR;
            $this->addResponse(
                "register_code"
                , $this->registrant->getStatus()
            );
            return false;
        }

        $wpCreated = $this->handleWordPressBackend();

        if (false === $wpCreated) {
            // registerType and registrant status is set in handleWordPressBackend() method
            $this->addResponse(
                "register_code"
                , $this->registrant->getStatus()
            );
            return false;
        }

        $this->addResponse(
            "register_code"
            , $this->registrant->getStatus()
        );
        $this->registerType = NewUserSubmit::REGISTER_TYPE_USER_REGULARLY_CREATED;

        return true;
    }

    private function handleWordPressBackend(): bool {
        // we do not handle WP stuff if we are not on production
        if (false === $this->environment->isProduction()) return true;
        if (null === $this->registrant) return false;

        $wpUser    = $this->wordPressService->createUser($this->registrant);
        $wpCreated = null !== $wpUser;

        if (false === $wpCreated) {
            $this->registrant->setStatus(Registrant::STATUS_WP_COULD_NOT_BE_CREATED);
            $this->registerType = NewUserSubmit::REGISTER_TYPE_WP_NOT_CREATED;
            return false;
        }

        $wpUserId = $wpUser["id"] ?? null;
        if (null !== $wpUserId) $wpUserId = (int) $wpUserId;
        $this->registrant->setWpUserId($wpUserId);
        $updated = $this->userManager->update($this->registrant);

        if (false === $updated) {
            $this->registrant->setStatus(Registrant::STATUS_WP_COULD_NOT_BE_CREATED);
            $this->registerType = NewUserSubmit::REGISTER_TYPE_WP_NOT_UPDATED;
            return false;
        }

        return true;
    }

    /**
     *
     */
    protected function onDestroy(): void {

        $arguments = [
            "registrant"           => $this->registrant
            , "immediately"        => $this->registerImmediately
            , "registered"         => false
            , "confirmation_token" => null
            , "wp_exists"          => false
        ];

        switch ($this->registerType) {
            case NewUserSubmit::REGISTER_TYPE_API_ERROR:
            case NewUserSubmit::REGISTER_TYPE_WP_NOT_CREATED:
            case NewUserSubmit::REGISTER_TYPE_WP_NOT_UPDATED:
                break;
            case NewUserSubmit::REGISTER_TYPE_USER_REGULARLY_CREATED:

                $arguments["registered"]         = true;
                $arguments["immediately"]        = true;
                $arguments["confirmation_token"] = $this->confirmationToken;
                $arguments["wp_exists"]          = true;
                break;
        }

        $content = $this->templateRenderer->loadTemplate(
            "registration_email"
            , $arguments + [
                "baseURL"   => Didapptic::getBaseURL(true)
                , "appName" => Didapptic::APP_NAME
                , "devText" => "Diese E-Mail stammt aus unserem Test-System. Bitte benachrichten Sie uns umgehend wenn Sie diese E-Mail versehentlich erhalten haben."
            ]
        );

        $notification = new Notification();
        $notification->setId(NotificationConstants::REGISTRATION_INFORMATION);
        $notification->setContent($content);
        $notification->setDelay(0);
        $notification->setSubject("Ihre Registrierung auf didapptic.com");
        $notification->setExecuted(false);
        $type = new Type();
        $type->setId(TypeConstants::NOTIFICATION[NotificationConstants::REGISTRATION_INFORMATION][IType::MAIL] ?? -1);
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

}
