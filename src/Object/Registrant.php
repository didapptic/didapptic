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
use function json_encode;

/**
 * Class Registrant
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Registrant extends User implements JsonSerializable {

    public const STATUS_VALID                                   = 0;
    public const STATUS_INVALID_FIRST_NAME                      = 1;
    public const STATUS_INVALID_LAST_NAME                       = 2;
    public const STATUS_INVALID_EMAIL                           = 3;
    public const STATUS_INVALID_PASSWORD                        = 4;
    public const STATUS_INVALID_PASSWORD_EQUALS_PASSWORD_REPEAT = 5;
    public const STATUS_INVALID_PASSWORD_STRENGTH               = 6;
    public const STATUS_INVALID_SUBJECTS                        = 7;
    public const STATUS_INVALID_EMAIL_ALREADY_REGISTERED        = 8;
    public const STATUS_INVALID_USERNAME_ALREADY_REGISTERED     = 9;
    public const STATUS_WP_COULD_NOT_BE_CREATED                 = 10;
    public const STATUS_INVALID_USER_NAME                       = 11;
    public const STATUS_TECHNICAL_DB_ERROR                      = 12;
    public const STATUS_TECHNICAL_TOKEN_ERROR                   = 13;

    /** @var array */
    private $subjects;
    /** @var bool */
    private $wantsNewsletter;
    /** @var DateTime */
    private $registerDate;
    /** @var int */
    private $status = Registrant::STATUS_VALID;

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() {
        return
            parent::jsonSerialize() +
            [
                "id"                      => $this->getId()
                , "username"              => $this->getName()
                , "password"              => $this->getPassword()
                , "first_name"            => $this->getFirstName()
                , "last_name"             => $this->getLastName()
                , "website_url"           => $this->getWebsiteURL()
                , "subjects"              => json_encode($this->getSubjects())
                , "wants_newsletter"      => $this->isWantsNewsletter()
                , "email"                 => $this->getEmail()
                , "status"                => $this->getStatus()
            ];
    }

    /**
     * @return array
     */
    public function getSubjects(): array {
        return $this->subjects;
    }

    /**
     * @param array $subjects
     */
    public function setSubjects(array $subjects): void {
        $this->subjects = $subjects;
    }

    /**
     * @return bool
     */
    public function isWantsNewsletter(): bool {
        return $this->wantsNewsletter;
    }

    /**
     * @param bool $wantsNewsletter
     */
    public function setWantsNewsletter(bool $wantsNewsletter): void {
        $this->wantsNewsletter = $wantsNewsletter;
    }

    /**
     * @return int
     */
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getRegisterDate(): DateTime {
        return $this->registerDate;
    }

    /**
     * @param DateTime $registerDate
     */
    public function setRegisterDate(DateTime $registerDate): void {
        $this->registerDate = $registerDate;
    }

    public function isValid(): bool {
        return Registrant::STATUS_VALID === $this->getStatus();
    }

    /**
     * @param User $user
     */
    public function merge(User $user): void {
        $this->setId($user->getId());
        $this->setName($user->getName());
        $this->setPassword($user->getPassword());
        $this->setPasswordRepeat($user->getPasswordRepeat());
        $this->setRoles($user->getRoles());
        $this->setFirstName($user->getFirstName());
        $this->setLastName($user->getLastName());
        $this->setWebsiteURL($user->getWebsiteURL());
        $this->setEmail($user->getEmail());
        $this->setWpUserId($user->getWpUserId());
    }

}
