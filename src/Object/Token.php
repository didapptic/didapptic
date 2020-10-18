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

/**
 * Class Token
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Token {

    /** @var int */
    private $id;
    /** @var string */
    private $token;
    /** @var User|null */
    private $user;
    /** @var DateTime */
    private $createTs;
    /** @var bool */
    private $active;

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
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void {
        $this->token = $token;
    }

    /**
     * @return null|User
     */
    public function getUser(): ?User {
        return $this->user;
    }

    /**
     * @param null|User $user
     */
    public function setUser(?User $user): void {
        $this->user = $user;
    }

    /**
     * @return DateTime
     */
    public function getCreateTs(): DateTime {
        return $this->createTs;
    }

    /**
     * @param DateTime $dateTime
     */
    public function setCreateTs(DateTime $dateTime): void {
        $this->createTs = $dateTime;
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void {
        $this->active = $active;
    }

}
