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

use DateTime;
use doganoo\PHPAlgorithms\Datastructure\Graph\Tree\BinarySearchTree;
use doganoo\SimpleRBAC\Common\IRole;

/**
 * Class Role
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Role implements IRole {

    public const DEFAULT_ROLE = 1;
    public const BASIC_ROLE   = 2;
    public const ADMIN_ROLE   = 3;
    /** @var string $name */
    private $name = "";
    /** @var null|BinarySearchTree $permissions */
    private $permissions = null;
    /** @var int $id */
    private $id = 0;
    /** @var DateTime */
    private $createTs;

    /**
     * @return BinarySearchTree|null
     */
    public function getPermissions(): ?BinarySearchTree {
        return $this->permissions;
    }

    /**
     * @param BinarySearchTree|null $permissions
     */
    public function setPermissions(?BinarySearchTree $permissions): void {
        $this->permissions = $permissions;
    }

    /**
     * @param mixed $object
     *
     * @return int
     */
    public function compareTo($object): int {
        if ($object instanceof IRole) {
            if ($this->getId() < $object->getId()) {
                return -1;
            }
            if ($this->getId() == $object->getId()) {
                return 0;
            }
            if ($this->getId() > $object->getId()) {
                return 1;
            }
        }
        return -1;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return DateTime
     */
    public function getCreateTs(): DateTime {
        return $this->createTs;
    }

    /**
     * @param DateTime $createTs
     */
    public function setCreateTs(DateTime $createTs): void {
        $this->createTs = $createTs;
    }

}
