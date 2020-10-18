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
use JsonSerializable;

/**
 * Class Menu
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Menu implements JsonSerializable {

    public const ID_LOGIN = 6;
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $href;
    /** @var int */
    private $order;
    /** @var bool */
    private $visible;
    /** @var DateTime */
    private $createTs;
    /** @var int */
    private $permissionId;

    public function jsonSerialize() {
        return [
            "id"              => $this->getId()
            , "name"          => $this->getName()
            , "href"          => $this->getHref()
            , "order"         => $this->getOrder()
            , "visible"       => $this->isVisible()
            , "permission_id" => $this->getPermissionId()
            , "create_ts"     => $this->getCreateTs()
        ];
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
     * @return string
     */
    public function getHref(): string {
        return $this->href;
    }

    /**
     * @param string $href
     */
    public function setHref(string $href): void {
        $this->href = $href;
    }

    /**
     * @return int
     */
    public function getOrder(): int {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): void {
        $this->order = $order;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible(bool $visible): void {
        $this->visible = $visible;
    }

    /**
     * @return int
     */
    public function getPermissionId(): int {
        return $this->permissionId;
    }

    /**
     * @param int $permissionId
     */
    public function setPermissionId(int $permissionId): void {
        $this->permissionId = $permissionId;
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
