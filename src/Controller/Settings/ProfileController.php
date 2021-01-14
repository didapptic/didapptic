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

namespace Didapptic\Controller\Settings;

use Didapptic\Controller\AbstractController;
use Didapptic\Didapptic;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Permission;
use Didapptic\Object\User;
use Didapptic\Repository\NotificationRepository;
use Didapptic\Repository\RoleRepository;
use Didapptic\Repository\UserRepository;
use Didapptic\Server;
use Didapptic\Service\Application\I18N\TranslationService;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\UserService;
use doganoo\INotify\Notification\INotification;
use doganoo\INotify\Notification\Type\IType;
use doganoo\INotify\Participant\IReceiver;
use doganoo\PHPAlgorithms\Algorithm\Traversal\PreOrder;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;
use Exception;

/**
 * Class ProfileController
 *
 * @package Didapptic\Controller\Settings
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class ProfileController extends AbstractController {

    /** @var TranslationService */
    private $translationService;
    /** @var PermissionService */
    private $permissionService;
    /** @var UserService */
    private $userService;
    /** @var UserRepository */
    private $userRepository;
    /** @var NotificationRepository */
    private $notificationRepository;
    /** @var bool */
    private $isAdmin;
    /** @var User */
    private $user;
    /** @var User */
    private $profile;
    /** @var RoleRepository */
    private $roleRepository;
    /** @var ArrayList */
    private $roles;
    /** @var ArrayList */
    private $notifications;

    public function __construct(
        TranslationService $translationService
        , PermissionService $permissionService
        , UserService $userService
        , UserRepository $userRepository
        , RoleRepository $roleRepository
        , NotificationRepository $notificationRepository
    ) {
        parent::__construct(
            $translationService->translate("Profil")
        );

        $this->translationService     = $translationService;
        $this->permissionService      = $permissionService;
        $this->userService            = $userService;
        $this->userRepository         = $userRepository;
        $this->roleRepository         = $roleRepository;
        $this->notificationRepository = $notificationRepository;

        $this->registerJavaScript(JavaScript::PROFILE);
    }

    protected function onCreate(): void {
        $this->isAdmin = $this->permissionService->hasPermission(
            $this->permissionService->toPermission(Permission::MENU_SETTINGS_ADMIN)
        );
        $user          = $this->userService->getUser();

        if (null === $user) {
            throw new Exception('user not found :(');
        }

        $this->user = $user;
        /** @var HashMap $users */
        $users               = Didapptic::getServer()->query(Server::USER_HASH_MAP);
        $this->profile       = $users->get(
            (int) $this->getArgument("userId")
        );
        $this->roles         = $this->roleRepository->getAll();
        $this->notifications = $this->notificationRepository->getAll();
    }

    protected function create(): ?string {

        if (null === $this->profile) {
            return $this->handleError(
                $this->translationService->translate("Der angegebene Benutzer wurde nicht gefunden")
            );
        }
        if (
            $this->profile->getId() !== $this->user->getId()
            && false === $this->isAdmin
        ) {
            return $this->handleError(
                $this->translationService->translate("Sie sind nicht berechtigt für diese Aktion")
            );
        }

        $template = $this->loadTemplate(
            View::PROFILE_VIEW
        );

        $profileRoles = [];

        if (null !== $this->profile->getRoles()) {
            $preOrder = new PreOrder($this->profile->getRoles());
            $preOrder->setCallable(function ($value) use (&$profileRoles) {
                $profileRoles[] = (int) $value;
            });
            $preOrder->traverse();
        }
        $profileNotifications = [];
        /** @var INotification $notification */
        foreach ($this->notifications as $index => $notification) {
            /** @var IType $type */
            foreach ($notification->getTypes() as $type) {
                if (false === $this->permissionService->hasPermission($type->getPermission())) {
                    $this->notifications->remove($index);
                    continue;
                }
                /** @var IReceiver $receiver */
                foreach ($type->getReceiverList() as $receiver) {
                    if ($this->profile->getId() !== $receiver->getId()) continue;
                    $profileNotifications[] = $type->getId();
                }
            }
        }

        return $template->render(
            [
                // strings
                "headline"                      => $this->translationService->translate("Profil")
                , "description"                 => $this->translationService->translate("Sie können hier die Benutzerangaben ändern")
                , "profileLabel"                => $this->translationService->translate("Benutzer")
                , "profileIdLabel"              => $this->translationService->translate("ID")
                , "profileIdPlaceholder"        => $this->translationService->translate("ID")
                , "profileNameLabel"            => $this->translationService->translate("Benutzername")
                , "profileNamePlaceholder"      => $this->translationService->translate("Benutzername")
                , "profileFirstNameLabel"       => $this->translationService->translate("Vorname")
                , "profileFirstNamePlaceholder" => $this->translationService->translate("Vorname")
                , "profileLastNameLabel"        => $this->translationService->translate("Nachname")
                , "profileLastNamePlaceholder"  => $this->translationService->translate("Nachname")
                , "profileEmailLabel"           => $this->translationService->translate("E-Mail")
                , "profileEmailPlaceholder"     => $this->translationService->translate("E-Mail")
                , "profilePasswordLabel"        => $this->translationService->translate("Passwort")
                , "profilePasswordPlaceholder"  => $this->translationService->translate("Passwort")
                , "profileWebsiteLabel"         => $this->translationService->translate("Webseite")
                , "profileWebsitePlaceholder"   => $this->translationService->translate("Webseite")
                , "profileNewsletterLabel"      => $this->translationService->translate("Newsletter")
                , "newsletterDescription"       => $this->translationService->translate("Registrieren")
                , "profileWpUserIdLabel"        => $this->translationService->translate("WordPress Blog")
                , "profileWpUserIdPlaceholder"  => $this->translationService->translate("WordPress Blog")
                , "rolesLabel"                  => $this->translationService->translate("Rollen")
                , "notificationsLabel"          => $this->translationService->translate("Benachrichtigungen")
                , "save"                        => $this->translationService->translate("Speichern")
                , "notificationsDescription"    => $this->translationService->translate("Hier können Sie einstellen welche Art von Benachrichtigung Sie auf welchem Wege erhalten möchten")
                , "noNotificationsDescription"  => $this->translationService->translate("Aktuell keine Benachrichtigungen möglich")
                , "rolesDescription"            => $this->translationService->translate("Hier können die Rollen des Benutzers festgelegt werden")

                // values
                , "profile"                     => $this->profile
                , "isAdmin"                     => $this->isAdmin
                , "roles"                       => $this->roles
                , "profileRoles"                => $profileRoles
                , "notifications"               => $this->notifications
                , "profileNotifications"        => $profileNotifications
                , "hasNotifications"            => 0 !== $this->notifications->length()
                , "passwordPolicy"              => "Das Passwort muss mindestens folgende Anforderungen erfüllen:<br><ul><li>mindestens 8 Zeichen</li><li>mindestens einen Großbuchstaben</li><li>mindestens ein Kleinbuchstaben</li><li>mindestens eine Zahl</li></ul>"
                , "typeNames"                   => [
                IType::MAIL         => $this->translationService->translate("E-Mail")
                , IType::LOG        => $this->translationService->translate("Datei Log")
                , IType::PLAIN_MAIL => $this->translationService->translate("Text E-Mail")
            ]
            ]
        );
    }

    private function handleError(string $text): string {
        $template = $this->loadTemplate(
            View::ALERT_VIEW
        );

        return $template->render(
            [
                "text" => $text
            ]
        );
    }

    protected function onDestroy(): void {

    }

}
