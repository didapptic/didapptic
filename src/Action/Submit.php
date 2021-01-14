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

use Didapptic\Didapptic;
use Didapptic\Object\Constant\Response as FrontendResponse;
use Didapptic\Object\Permission;
use Didapptic\Service\Slim\Response\ResponseService;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Submit\AbstractSubmit;
use Didapptic\Submit\AddCategorySubmit;
use Didapptic\Submit\AddSubjectSubmit;
use Didapptic\Submit\AddTagSubmit;
use Didapptic\Submit\DeleteAppSubmit;
use Didapptic\Submit\DeleteFileSubmit;
use Didapptic\Submit\DeleteMaterialSubmit;
use Didapptic\Submit\LoginSubmit;
use Didapptic\Submit\MaterialPasswordCheckSubmit;
use Didapptic\Submit\NewAppSubmit;
use Didapptic\Submit\NewContactSubmit;
use Didapptic\Submit\NewMaterialSubmit;
use Didapptic\Submit\NewUserSubmit;
use Didapptic\Submit\PasswordLostSubmit;
use Didapptic\Submit\PasswordUpdateSubmit;
use Didapptic\Submit\RemainingApps;
use Didapptic\Submit\Settings\UpdateUserSubmit;
use Didapptic\Submit\UpdateAppSubmit;
use doganoo\SimpleRBAC\Handler\PermissionHandler;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

/**
 * Class Submit
 *
 * @package Didapptic\Action
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Submit extends Action {

    private const PATTERN_CONFIGURATION = [
        // HTTP DELETE
        "/v1/material/file/{id}/"                    => [
            "name"         => DeleteMaterialSubmit::class
            , "permission" => Permission::MENU_DELETE_MATERIAL
        ]
        , "/v1/file/{id}/{materialId}/"              => [
            "name"         => DeleteFileSubmit::class
            , "permission" => Permission::MENU_DELETE_FILE
        ]
        , "/v1/applications/delete/{appId}/"         => [
            "name"         => DeleteAppSubmit::class
            , "permission" => Permission::APP_DELETION
        ]

        // HTTP PUT
        , "/menu/new-user/new/submit/"               => [
            "name"         => NewUserSubmit::class
            , "permission" => Permission::SUBMIT_NEW_USER
        ]
        , "/menu/password-lost/submit/"              => [
            "name"         => PasswordLostSubmit::class
            , "permission" => Permission::MENU_PASSWORD_LOST
        ]
        , "/v1/menu/contact/new/submit/"             => [
            "name"         => NewContactSubmit::class
            , "permission" => Permission::MENU_CONTACT
        ]
        , "/menu/login/submit/"                      => [
            "name"         => LoginSubmit::class
            , "permission" => Permission::DEFAULT_PERMISSION
        ]
        , "/menu/profile/add/"                       => [
            "name"         => UpdateUserSubmit::class
            , "permission" => Permission::DEFAULT_PERMISSION
        ]

        // HTTP POST
        , "/v1/material/files/upload/"               => [
            "name"         => NewMaterialSubmit::class
            , "permission" => Permission::MENU_NEW_MATERIAL
        ]
        , "/v1/material/password/check/"             => [
            "name"         => MaterialPasswordCheckSubmit::class
            , "permission" => Permission::DEFAULT_PERMISSION
        ]
        , "/menu/update-app/update/submit/"          => [
            "name"         => UpdateAppSubmit::class
            , "permission" => Permission::SUBMIT_NEW_APPLICATION
        ]
        , "/password/update/"                        => [
            "name"         => PasswordUpdateSubmit::class
            , "permission" => Permission::DEFAULT_PERMISSION //TODO replace with user == owner if simple-rbac supports it
        ]
        , "/v1/subject/new/"                         => [
            "name"         => AddSubjectSubmit::class
            , "permission" => Permission::SUBMIT_NEW_SUBJECT
        ]
        , "/v1/category/new/"                        => [
            "name"         => AddCategorySubmit::class
            , "permission" => Permission::SUBMIT_NEW_CATEGORY
        ]
        , "/v1/tag/new/"                             => [
            "name"         => AddTagSubmit::class
            , "permission" => Permission::SUBMIT_NEW_TAG
        ]
        , "/menu/new-app/new/submit/"                => [
            "name"         => NewAppSubmit::class
            , "permission" => Permission::SUBMIT_NEW_APPLICATION
        ]

        // API STUFF
        , "/v1/all-apps/remaining-apps/{chunkSize}/" => [
            "name"         => RemainingApps::class
            , "permission" => Permission::DEFAULT_PERMISSION
        ]
    ];

    public function __invoke(
        Request $request
        , Response $response
        , array $arguments
    ): Response {

        /** @var PermissionHandler $permissionHandler */
        $permissionHandler = Didapptic::getServer()->query(PermissionHandler::class);
        /** @var ResponseService $responseService */
        $responseService = Didapptic::getServer()->query(ResponseService::class);
        /** @var PermissionService $permissionService */
        $permissionService = Didapptic::getServer()->query(PermissionService::class);
        /** @var Route $route */
        $route   = $request->getAttributes()['route'];
        $pattern = $route->getPattern();

        $configuration = Submit::PATTERN_CONFIGURATION[$pattern];

        $name       = $configuration["name"];
        $permission = $configuration["permission"] ?? null;

        if (null !== $permission && false === $permissionHandler->hasPermission($permissionService->toPermission($permission))) {
            return $responseService->getSubmitUnauthorizedResponse($response);
        }

        /** @var AbstractSubmit $submit */
        $submit = $this->createObject($name);
        $submit->setArguments(
            array_merge(
                (array) $request->getParsedBody() ?? []
                , $arguments
                , [
                    "files" => $_FILES
                ]
            ));
        $ran = $submit->run();

        $response->getBody()->write(
            (string) json_encode([
                FrontendResponse::FIELD_NAME_RESPONSE_CODE =>
                    true === $ran
                        ? FrontendResponse::OK
                        : FrontendResponse::FAILED
                , FrontendResponse::FIELD_NAME_CONTENT     => $submit->getResponse()
            ])

        );

        return $response;
    }

}
