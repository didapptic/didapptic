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

namespace Didapptic\Object;

use Didapptic\Repository\PermissionRepository;
use Didapptic\Service\User\UserService;
use doganoo\PHPAlgorithms\Datastructure\Graph\Tree\BinarySearchTree;
use doganoo\SimpleRBAC\Common\IDataProvider;
use doganoo\SimpleRBAC\Common\IPermission;
use doganoo\SimpleRBAC\Common\IUser;

/**
 * Class PermissionDataProvider
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class PermissionDataProvider implements IDataProvider {

    /** @var PermissionRepository */
    private $permissionManager;
    /** @var UserService */
    private $userService;

    public function __construct(
        PermissionRepository $permissionManager
        , UserService $userService
    ) {
        $this->permissionManager = $permissionManager;
        $this->userService       = $userService;
    }

    /**
     * the user whose permissions should be validated
     *
     * @return IUser
     */
    public function getUser(): ?IUser {
        return $this->userService->getUser();
    }

    /**
     * returns the permission Object that belongs to $id
     *
     * @param int $id
     *
     * @return IPermission|null
     */
    public function getPermission(int $id): ?IPermission {
        return $this->permissionManager->getPermissionById($id);
    }

    /**
     * all default permissions that are public for all users
     *
     * @return BinarySearchTree
     */
    public function getDefaultPermissions(): BinarySearchTree {
        return $this->permissionManager->getDefaultPermissionIds();
    }

}
