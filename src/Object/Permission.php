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

use doganoo\PHPAlgorithms\Datastructure\Graph\Tree\BinarySearchTree;
use doganoo\SimpleRBAC\Common\IContext;
use doganoo\SimpleRBAC\Common\IPermission;

/**
 * Class Permission
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Permission implements IPermission {

    public const NO_PERMISSION                 = -1;
    public const MENU_HINTS                    = 1;
    public const MENU_CONTACT                  = 2;
    public const MENU_MATERIAL                 = 3;
    public const MENU_ABOUT                    = 4;
    public const MENU_NEW_ENTRY                = 5;
    public const MENU_LOGIN                    = 6;
    public const DEFAULT_PERMISSION            = 7;
    public const SUBMIT_NEW_APPLICATION        = 9;
    public const MENU_PRIVACY                  = 10;
    public const MENU_IMPRINT                  = 11;
    public const SUBMIT_NEW_USER               = 12;
    public const MENU_NEW_USER                 = 13;
    public const APP_DELETION                  = 17;
    public const APP_EDIT                      = 18;
    public const MENU_PARTNER                  = 19;
    public const MENU_PASSWORD_LOST            = 20;
    public const MENU_NEW_MATERIAL             = 23;
    /** @deprecated  */
    public const MENU_USER_MANAGEMENT          = 25;
    public const MENU_DELETE_MATERIAL          = 26;
    public const MENU_DELETE_FILE              = 27;
    public const SUBMIT_NEW_SUBJECT            = 28;
    public const SUBMIT_NEW_CATEGORY           = 29;
    public const SUBMIT_NEW_TAG                = 30;
    public const MENU_SETTINGS                 = 31;
    public const MENU_PROFILE                  = 32;
    public const MENU_SETTINGS_ADMIN           = 33;
    public const NOTIFICATION_REMOVED_APP_MAIL = 34;
    public const NOTIFICATION_CONTACT_MAIL     = 35;
    public const NOTIFICATION_REGISTER_MAIL    = 36;
    public const NOTIFICATION_PASSWORD_MAIL    = 37;
    public const NOTIFICATION_NEW_APP_MAIL     = 38;
    public const MENU_SETTINGS_EDIT_USERS      = 39;

    /** @var int $id */
    private $id = -1;
    /** @var string $name */
    private $name = "";
    /** @var null|BinarySearchTree $roles */
    private $roles = null;

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
     * @return BinarySearchTree|null
     */
    public function getRoles(): ?BinarySearchTree {
        return $this->roles;
    }

    /**
     * @param BinarySearchTree|null $roles
     */
    public function setRoles(?BinarySearchTree $roles): void {
        $this->roles = $roles;
    }

    /**
     * @param mixed $object
     *
     * @return int
     */
    public function compareTo($object): int {
        if ($object instanceof IPermission) {
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
     * @inheritDoc
     */
    public function getContext(): ?IContext {
        return null;
    }

}
