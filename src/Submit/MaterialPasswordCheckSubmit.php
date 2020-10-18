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

use Didapptic\Didapptic;
use Didapptic\Object\Permission;
use Didapptic\Repository\MaterialRepository;
use Didapptic\Service\Material\MaterialService;
use Didapptic\Service\Session\SessionService;
use Didapptic\Service\User\Permission\PermissionService;

/**
 * Class MaterialPasswordCheckSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MaterialPasswordCheckSubmit extends AbstractSubmit {

    /** @var array */
    private $arguments;
    /** @var MaterialRepository */
    private $materialManager;
    /** @var MaterialService */
    private $materialService;
    /** @var PermissionService */
    private $permissionService;
    /** @var SessionService */
    private $sessionService;

    public function __construct(
        MaterialRepository $materialManager
        , MaterialService $materialService
        , PermissionService $permissionService
        , SessionService $sessionService
    ) {
        parent::__construct();
        $this->materialManager   = $materialManager;
        $this->materialService   = $materialService;
        $this->permissionService = $permissionService;
        $this->sessionService    = $sessionService;
    }

    protected function valid(): bool {
        $this->arguments = $this->getArguments();
        return
            null !== $this->arguments["materialId"]
            && null !== $this->arguments["password"];
    }

    protected function onCreate(): void {

    }

    /**
     * @return bool
     */
    protected function create(): bool {
        $material            = $this->materialManager->get(
            (int) $this->arguments["materialId"]
        );
        $canDeleteSingleFile = $this->permissionService->hasPermission(
            $this->permissionService->toPermission(Permission::MENU_DELETE_FILE)
        );

        $token = uniqid("", false);

        if (null === $material) return false;
        if (null === $material->getPassword()) {
            $this->sessionService->set($token, "true");

            $this->setResponse(
                [
                    "files"                    => $material->getFiles()
                    , "material_file_url"      => Didapptic::getBaseURL(true) . "/v1/material/file/"
                    , "can_delete_single_file" => $canDeleteSingleFile
                    , "token"                  => $token
                ]
            );
            return true;
        }
        if (true === $this->materialService->verifyPassword($material, $this->arguments["password"])) {
            $this->sessionService->set($token, "true");
            $this->setResponse(
                [
                    "files"                    => $material->getFiles()
                    , "material_file_url"      => Didapptic::getBaseURL(true) . "/v1/material/file/"
                    , "can_delete_single_file" => $canDeleteSingleFile
                    , "token"                  => $token
                ]
            );
            return true;
        }
        return false;
    }

    protected function onDestroy(): void {

    }

}
