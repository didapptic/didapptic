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

namespace Didapptic\Repository;

use Didapptic\Object\Permission;
use Didapptic\Object\Role;
use doganoo\PHPAlgorithms\Datastructure\Graph\Tree\BinarySearchTree;
use doganoo\PHPUtil\Storage\PDOConnector;
use doganoo\SimpleRBAC\Common\IPermission;
use PDO;

/**
 * Class PermissionManager
 *
 * @package Didapptic\Manager
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class PermissionRepository {

    private $connector = null;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function getDefaultPermissionIds(): BinarySearchTree {
        $tree               = new BinarySearchTree();
        $sql                = "select
                    p.id
                 from permission p 
                 left join permission_role pr on p.id = pr.permission_id
                 left join role r on pr.role_id = r.id
                 where r.id = :default_role_id;";
        $statement          = $this->connector->prepare($sql);
        $defaultPermissions = Role::DEFAULT_ROLE;
        $statement->bindParam(":default_role_id", $defaultPermissions);
        if (null === $statement) {
            return $tree;
        }
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id         = $row[0];
            $permission = $this->toPermission((int) $id);
            $tree->insertValue($permission);
        }
        return $tree;
    }

    /**
     * not so nice, but it does what it should to
     *
     * @param int $id
     *
     * @return IPermission
     */
    private function toPermission(int $id): IPermission {
        $permissionRoles = $this->getPermissionRoles($id);
        $permission      = new Permission();
        $permission->setId($id);
        $permission->setRoles($permissionRoles);
        return $permission;
    }

    public function getPermissionRoles(int $id): ?BinarySearchTree {
        $sql       = "select r.id
                    from role r 
                    left join permission_role pr on r.id = pr.role_id
                    left join permission p on pr.permission_id = p.id
                where p.id = :id;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return null;
        }
        $statement->bindParam("id", $id);
        $statement->execute();
        $tree = new BinarySearchTree();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id = (int) $row[0];
            $tree->insertValue($id);
        }
        return $tree;
    }

    public function getPermissionById(int $id): ?IPermission {
        $sql       = "select 
                        id
                        , name
                from permission p 
                where p.id = :id;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return null;
        }
        $statement->bindParam("id", $id);
        $statement->execute();
        $permission = new Permission();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id   = $row[0];
            $name = $row[1];
            $permission->setId((int) $id);
            $permission->setName((string) $name);
            $roles = $this->getPermissionRoles((int) $id);
            $permission->setRoles($roles);
        }
        return $permission;
    }

}
