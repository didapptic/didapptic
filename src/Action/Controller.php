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

namespace Didapptic\Action;

use Didapptic\Controller\AboutViewController;
use Didapptic\Controller\AbstractController;
use Didapptic\Controller\AppDetailViewController;
use Didapptic\Controller\ContactViewController;
use Didapptic\Controller\EditAppController;
use Didapptic\Controller\HintsViewController;
use Didapptic\Controller\ImprintViewController;
use Didapptic\Controller\LoginViewController;
use Didapptic\Controller\LogoutController;
use Didapptic\Controller\MainViewController;
use Didapptic\Controller\MaterialViewController;
use Didapptic\Controller\NewAppController;
use Didapptic\Controller\NewUserViewController;
use Didapptic\Controller\PartnerViewController;
use Didapptic\Controller\PasswordLostController;
use Didapptic\Controller\PrivacyViewController;
use Didapptic\Controller\ResetPasswordViewController;
use Didapptic\Controller\Settings\ProfileController;
use Didapptic\Controller\Settings\SettingsController;
use Didapptic\Didapptic;
use Didapptic\Middleware\MaterialViewAuthentication;
use Didapptic\Middleware\RegistrationAuthentication;
use Didapptic\Object\Environment;
use Didapptic\Object\Permission;
use Didapptic\Service\Slim\Response\ResponseService;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\UserService;
use Psr\Container\ContainerInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;
use Tuupola\Middleware\HttpBasicAuthentication;

/**
 * Class Controller
 *
 * @package Didapptic\Action
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Controller extends Action {

    private const PATTERN_CONFIGURATION = [
        "/menu/material/"                 => [
            "name"              => MaterialViewController::class
            , "permission"      => Permission::MENU_MATERIAL
            , "auth_middleware" => MaterialViewAuthentication::class
        ]
        , "/menu/hints/"                  => [
            "name"         => HintsViewController::class
            , "permission" => Permission::MENU_HINTS
        ]
        , "/menu/about/"                  => [
            "name"         => AboutViewController::class
            , "permission" => Permission::MENU_ABOUT
        ]
        , "/menu/contact/"                => [
            "name"         => ContactViewController::class
            , "permission" => Permission::MENU_CONTACT
        ]
        , "/menu/imprint/"                => [
            "name"         => ImprintViewController::class
            , "permission" => Permission::MENU_IMPRINT
        ]
        , "/menu/privacy/"                => [
            "name"         => PrivacyViewController::class
            , "permission" => Permission::MENU_PRIVACY
        ]
        , "/menu/partner/"                => [
            "name"         => PartnerViewController::class
            , "permission" => Permission::MENU_PARTNER
        ]
        , "/menu/login/"                  => [
            "name"         => LoginViewController::class
            , "permission" => Permission::MENU_LOGIN
        ]
        , "/menu/settings/"               => [
            "name"         => SettingsController::class
            , "permission" => Permission::MENU_SETTINGS
        ]
        , "/menu/profile/{userId}/"       => [
            "name"         => ProfileController::class
            , "permission" => Permission::MENU_PROFILE
        ]
        , "/menu/password-lost/"          => [
            "name"         => PasswordLostController::class
            , "permission" => Permission::MENU_PASSWORD_LOST
        ]
        , "/"                             => [
            "name"         => MainViewController::class
            , "alias"      => "home"
            , "permission" => Permission::DEFAULT_PERMISSION
        ]
        , "/menu/new-app/"                => [
            "name"            => NewAppController::class
            , "permission"    => Permission::MENU_NEW_ENTRY
            , "requires_user" => true
        ]
        , "/menu/new-user/"               => [
            "name"              => NewUserViewController::class
            , "permission"      => Permission::MENU_NEW_USER
            , "auth_middleware" => RegistrationAuthentication::class
        ]
        , "/menu/logout/"                 => [
            "name"          => LogoutController::class
            , "permission"  => Permission::DEFAULT_PERMISSION
            , "redirect_to" => "home"
        ]
        , "/menu/password-lost/{token}/"  => [
            "name"         => ResetPasswordViewController::class
            , "permission" => Permission::MENU_PASSWORD_LOST
        ]
        , "/v1/app-modal-detail/{appId}/" => [
            "name"           => AppDetailViewController::class
            , "permission"   => Permission::DEFAULT_PERMISSION
            , "content_only" => true
        ]
        , "/menu/edit-app/{storeId}/"     => [
            "name"            => EditAppController::class
            , "permission"    => Permission::APP_EDIT
            , "requires_user" => true
        ]
    ];

    /** @var ContainerInterface */
    private $container;

    /**
     * Controller constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $arguments
     *
     * @return Response
     */
    public function __invoke(
        Request $request
        , Response $response
        , array $arguments
    ) {
        /** @var UserService $userService */
        $userService = Didapptic::getServer()->query(UserService::class);
        /** @var ResponseService $responseService */
        $responseService = Didapptic::getServer()->query(ResponseService::class);
        /** @var PermissionService $permissionService */
        $permissionService = Didapptic::getServer()->query(PermissionService::class);
        /** @var Environment $environment */
        $environment = Didapptic::getServer()->query(Environment::class);

        /** @var Route $route */
        $route   = $request->getAttributes()['route'];
        $pattern = $route->getPattern();
        $user    = $userService->getUser();

        $configuration = Controller::PATTERN_CONFIGURATION[$pattern];

        $name           = $configuration["name"];
        $requiresUser   = $configuration["requires_user"] ?? false;
        $redirectTo     = $configuration["redirect_to"] ?? null;
        $contentOnly    = $configuration["content_only"] ?? false;
        $permission     = $configuration["permission"] ?? null;
        $authMiddleware = $configuration["auth_middleware"] ?? null;

        if (null !== $authMiddleware) {
            $route->add(new HttpBasicAuthentication([
                "realm"         => "Protected",
                "authenticator" => Didapptic::getServer()->query($authMiddleware),
                "secure"        => $environment->isSecure()
            ]));
        }

        if (true === $requiresUser && null === $user) {
            return $responseService->getControllerUnauthorizedResponse($response);
        }

        if (null !== $permission && false === $permissionService->hasPermission($permissionService->toPermission($permission))) {
            return $responseService->getControllerUnauthorizedResponse($response);
        }

        /** @var AbstractController $controller */
        $controller = $this->createObject($name);
        $controller->setArguments(
            array_merge(
                $arguments ?? []
                , $request->getParsedBody() ?? []
                , $_GET
            )
        );
        $response->getBody()->write($controller->run($contentOnly));

        if (null !== $redirectTo) {

            return $response->withRedirect(
                (string) $request->getUri()->withPath($this->container->get("router")->pathFor('home'))
            );
        }

        return $response;
    }

}
