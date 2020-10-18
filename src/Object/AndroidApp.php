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

use JsonSerializable;

/**
 * Class AndroidApp
 *
 * @package    Didapptic\Object
 * @author     Dogan Ucar <dogan@dogan-ucar.de>
 * @deprecated use App instead, set default values
 */
class AndroidApp extends App implements JsonSerializable {

    private $privacyPolicy = "";
    private $publisherId   = "";

    private $available         = true;
    private $permissions       = [];
    private $developerEmail;
    private $related           = [];
    private $numberOfDownloads = 0;

    public function jsonSerialize() {

        return parent::jsonSerialize() + [
                "privacy_policy"           => $this->getPrivacyPolicy()
                , "publisher_id"           => $this->getPublisherId()
                , "available"              => $this->isAvailable()
                , "play_store_permissions" => $this->getPermissions()
                , "developer_email"        => $this->getDeveloperEmail()
                , "related_apps"           => $this->getRelated()
                , "number_of_downloads"    => $this->getNumberOfDownloads()
            ];

    }

    /**
     * @return string
     */
    public function getPrivacyPolicy(): string {
        return $this->privacyPolicy;
    }

    /**
     * @param string $privacyPolicy
     */
    public function setPrivacyPolicy(string $privacyPolicy): void {
        $this->privacyPolicy = $privacyPolicy;
    }

    /**
     * @return string
     */
    public function getPublisherId(): string {
        return $this->publisherId;
    }

    /**
     * @param string $publisherId
     */
    public function setPublisherId(string $publisherId): void {
        $this->publisherId = $publisherId;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailable(bool $available): void {
        $this->available = $available;
    }

    /**
     * @return array
     */
    public function getPermissions(): array {
        return $this->permissions;
    }

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions): void {
        $this->permissions = $permissions;
    }

    /**
     * @return mixed
     */
    public function getDeveloperEmail() {
        return $this->developerEmail;
    }

    /**
     * @param mixed $developerEmail
     */
    public function setDeveloperEmail($developerEmail): void {
        $this->developerEmail = $developerEmail;
    }

    /**
     * @return array
     */
    public function getRelated(): ?array {
        return $this->related;
    }

    /**
     * @param array $related
     */
    public function setRelated(?array $related): void {
        $this->related = $related;
    }

    /**
     * @return int
     */
    public function getNumberOfDownloads(): int {
        return $this->numberOfDownloads;
    }

    /**
     * @param int $numberOfDownloads
     */
    public function setNumberOfDownloads(int $numberOfDownloads): void {
        $this->numberOfDownloads = $numberOfDownloads;
    }

}
