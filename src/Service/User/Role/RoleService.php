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

namespace Didapptic\Service\User\Role;

use doganoo\PHPAlgorithms\Algorithm\Traversal\PreOrder;
use doganoo\PHPAlgorithms\Datastructure\Graph\Tree\BinarySearchTree;

/**
 * Class RoleService
 *
 * @package Didapptic\Service\User\Role
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class RoleService {

    /**
     * @param array $array
     *
     * @return BinarySearchTree
     */
    public function toRoles(array $array): BinarySearchTree {
        return BinarySearchTree::createFromArrayWithMinimumHeight($array);
    }

    /**
     * @param BinarySearchTree $roles
     *
     * @return array
     */
    public function toArray(BinarySearchTree $roles): array {
        $rolesArray = [];
        $preOrder   = new PreOrder($roles);
        $preOrder->setCallable(function ($id) use (&$rolesArray) {
            $rolesArray[] = $id;
        });
        $preOrder->traverse();
        return $rolesArray;
    }

}
