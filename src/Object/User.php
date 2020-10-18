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
use doganoo\SimpleRBAC\Common\IUser;
use JsonSerializable;

/**
 * Class User
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class User implements IUser, JsonSerializable {

    /** @var int */
    private $id = -1;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $name;
    /** @var string */
    private $email;
    /** @var string */
    private $password;
    /** @var string */
    private $passwordRepeat;
    /** @var string */
    private $websiteURL;
    /** @var bool */
    private $newsletter;
    /** @var string|null */
    private $registerToken;
    /** @var string|null */
    private $confirmationToken;
    /** @var DateTime|null */
    private $createTs;
    /** @var DateTime|null */
    private $updateTs;
    /** @var BinarySearchTree|null */
    private $roles;
    /** @var int|null */
    private $wpUserId;
    /** @var string|null */
    private $plainPassword;
    /** @var string|null */
    private $plainPasswordRepeat;

    /**
     * @return mixed
     */
    public function getPasswordRepeat() {
        return $this->passwordRepeat;
    }

    /**
     * @param mixed $passwordRepeat
     */
    public function setPasswordRepeat($passwordRepeat): void {
        $this->passwordRepeat = $passwordRepeat;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void {
        $this->email = $email;
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
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void {
        $this->password = $password;
    }

    /**
     * returns the users roles
     *
     * @return BinarySearchTree|null
     */
    public function getRoles(): ?BinarySearchTree {
        return $this->roles;
    }

    /**
     * sets the users roles
     *
     * @param BinarySearchTree|null $roles
     */
    public function setRoles(?BinarySearchTree $roles): void {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getWebsiteURL() {
        return $this->websiteURL;
    }

    /**
     * @param mixed $websiteURL
     */
    public function setWebsiteURL($websiteURL): void {
        $this->websiteURL = $websiteURL;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() {
        return
            [
                "id"                      => $this->id
                , "name"                  => $this->name
                , "first_name"            => $this->firstName
                , "last_name"             => $this->lastName
                , "website_url"           => $this->websiteURL
                , "email"                 => $this->email
                , "wp_user_id"            => $this->getWpUserId()
                , "plain_password"        => "XXX SENSITIVE DATA - REMOVED XXX"
                , "plain_password_repeat" => "XXX SENSITIVE DATA - REMOVED XXX"
            ];
    }

    public function getWpUserId(): ?int {
        return $this->wpUserId;
    }

    public function setWpUserId(?int $wpUserId): void {
        $this->wpUserId = $wpUserId;
    }

    /**
     * @return bool
     */
    public function isNewsletter(): bool {
        return $this->newsletter;
    }

    /**
     * @param bool $newsletter
     */
    public function setNewsletter(bool $newsletter): void {
        $this->newsletter = $newsletter;
    }

    /**
     * @return string|null
     */
    public function getRegisterToken(): ?string {
        return $this->registerToken;
    }

    /**
     * @param string|null $registerToken
     */
    public function setRegisterToken(?string $registerToken): void {
        $this->registerToken = $registerToken;
    }

    /**
     * @return string|null
     */
    public function getConfirmationToken(): ?string {
        return $this->confirmationToken;
    }

    /**
     * @param string|null $confirmationToken
     */
    public function setConfirmationToken(?string $confirmationToken): void {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return DateTime|null
     */
    public function getCreateTs(): ?DateTime {
        return $this->createTs;
    }

    /**
     * @param DateTime|null $createTs
     */
    public function setCreateTs(?DateTime $createTs): void {
        $this->createTs = $createTs;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdateTs(): ?DateTime {
        return $this->updateTs;
    }

    /**
     * @param DateTime|null $updateTs
     */
    public function setUpdateTs(?DateTime $updateTs): void {
        $this->updateTs = $updateTs;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string|null
     */
    public function getPlainPasswordRepeat(): ?string {
        return $this->plainPasswordRepeat;
    }

    /**
     * @param string|null $plainPasswordRepeat
     */
    public function setPlainPasswordRepeat(?string $plainPasswordRepeat): void {
        $this->plainPasswordRepeat = $plainPasswordRepeat;
    }

}
