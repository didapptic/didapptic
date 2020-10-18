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

namespace Didapptic\Service\User\Permission;

use Didapptic\Object\Permission;
use Didapptic\Repository\PermissionRepository;
use doganoo\SimpleRBAC\Common\IPermission;
use doganoo\SimpleRBAC\Handler\PermissionHandler;

/**
 * Class PermissionService
 *
 * @package Didapptic\Service\User\Permission
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class PermissionService {

    /** @var PermissionRepository */
    private $permissionManager;
    /** @var PermissionHandler */
    private $permissionHandler;

    public function __construct(
        PermissionRepository $permissionManager
        , PermissionHandler $permissionHandler
    ) {
        $this->permissionManager = $permissionManager;
        $this->permissionHandler = $permissionHandler;
    }

    public function toPermission(int $id): IPermission {
        $permissionRoles = $this->permissionManager->getPermissionRoles($id);
        $permission      = new Permission();
        $permission->setId($id);
        $permission->setRoles($permissionRoles);
        return $permission;
    }

    public function hasPermission(IPermission $permission): bool {
        return $this->permissionHandler->hasPermission($permission);
    }

}
