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
use Didapptic\Object\Constant\View;
use Didapptic\Object\Permission;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Application\I18N\TranslationService;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\UserService;

/**
 * Class SettingsController
 *
 * @package Didapptic\Controller\Settings
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class SettingsController extends AbstractController {

    /** @var TranslationService */
    private $translationService;
    /** @var UserRepository */
    private $userRepository;
    /** @var PermissionService */
    private $permissionService;
    /** @var bool */
    private $isAdmin;
    /** @var UserService */
    private $userService;

    public function __construct(
        TranslationService $translationService
        , UserRepository $userRepository
        , PermissionService $permissionService
        , UserService $userService
    ) {
        parent::__construct($translationService->translate("Einstellungen"));

        $this->translationService = $translationService;
        $this->userRepository     = $userRepository;
        $this->permissionService  = $permissionService;
        $this->userService        = $userService;
    }

    protected function onCreate(): void {

        $this->isAdmin = $this->permissionService->hasPermission(
            $this->permissionService->toPermission(Permission::MENU_SETTINGS_EDIT_USERS)
        );

    }

    protected function create(): ?string {

        $template = $this->loadTemplate(
            $this->getTemplatePath()
            , View::SETTINGS_VIEW
        );

        $users = $this->userRepository->getAll();
        return $template->render(
            [
                // strings
                "headline"           => $this->translationService->translate("Allgemeine Einstellungen")
                , "description"      => $this->translationService->translate("Hier können Sie allgemeine Einstellungen treffen")
                , "usersLabel"       => $this->translationService->translate("Benutzer")
                , "usersDescription" => $this->translationService->translate("Nachfolgend können Sie Benutzer editieren.")

                // values
                , "users"            => $users
                , "isAdmin"          => $this->isAdmin
                , "baseUrl"          => Didapptic::getBaseURL(true)
                , "editUserRoute"    => "/menu/profile/"
                , "myUser"             => $this->userService->getUser()
            ]
        );
    }

    protected function onDestroy(): void {

    }

}
