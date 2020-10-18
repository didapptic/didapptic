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
 * Class App
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class App implements JsonSerializable {

    public const IOS          = 1;
    public const IOS_NAME     = "iOS";
    public const ANDROID      = 2;
    public const ANDROID_NAME = "Android";

    /** @var int */
    private $id;
    /** @var array */
    private $urls;
    /** @var string */
    private $name;
    /** @var double */
    private $price;
    /** @var double */
    private $rating;
    /** @var int */
    private $operatingSystem;
    /** @var string */
    private $description;
    /** @var string */
    private $publisherEmail;
    /** @var string */
    private $storeId;
    /** @var DateTime */
    private $lastUpdated;
    /** @var DateTime|null */
    private $releaseDate;
    /** @var double */
    private $usage;
    /** @var double */
    private $resultsQuality;
    /** @var double */
    private $presentability;
    /** @var string */
    private $didacticComment;
    /** @var string */
    private $didacticRemark;
    /** @var int */
    private $privacy;
    /** @var string */
    private $privacyComment;
    /** @var int */
    private $author;
    /** @var int */
    private $recommendation;
    /** @var array */
    private $subjects;
    /** @var array */
    private $tags;
    /** @var array */
    private $categories;
    /** @var string */
    private $storeUrl;
    /** @var string */
    private $iconUrl;
    /** @var double */
    private $avgRating;
    /** @var string */
    private $iosPrivacy;
    /** @var int */
    private $privacyCode;
    /** @var string */
    private $minimumOsVersion;
    /** @var array */
    private $supportedDevices;
    /** @var string */
    private $bundleId;
    /** @var DateTime */
    private $deleteTs;
    /** @var DateTime|null */
    private $lastUserUpdateTs;
    /** @var DateTime */
    private $createTs;
    /** @var string */
    private $priceCurrency;
    /** @var string */
    private $version;
    /** @var string */
    private $developer;
    /** @var string */
    private $developerWebsite;

    /**
     * @return int
     */
    public function getPrivacyCode(): int {
        return $this->privacyCode;
    }

    /**
     * @param int $privacyCode
     */
    public function setPrivacyCode(int $privacyCode): void {
        $this->privacyCode = $privacyCode;
    }

    /**
     * @return string
     */
    public function getBundleId(): string {
        return $this->bundleId;
    }

    /**
     * @param string $bundleId
     */
    public function setBundleId(string $bundleId): void {
        $this->bundleId = $bundleId;
    }

    public function jsonSerialize() {
        return [
            "id"                    => $this->getId()
            , "urls"                => $this->getUrls()
            , "name"                => $this->getName()
            , "price"               => $this->getPrice()
            , "rating"              => $this->getRating()
            , "operating_system"    => $this->getOperatingSystem()
            , "description"         => $this->getDescription()
            , "store_id"            => $this->getStoreId()
            , "last_updated"        => $this->getLastUpdated()
            , "release_date"        => $this->getReleaseDate()
            , "usage"               => $this->getUsage()
            , "results_quality"     => $this->getResultsQuality()
            , "presentability"      => $this->getPresentability()
            , "didactic_comment"    => $this->getDidacticComment()
            , "didactic_remark"     => $this->getDidacticRemark()
            , "privacy"             => $this->getPrivacy()
            , "privacy_comment"     => $this->getPrivacyComment()
            , "author"              => $this->getAuthor()
            , "recommendation"      => $this->getRecommendation()
            , "subjects"            => $this->getSubjects()
            , "tags"                => $this->getTags()
            , "categories"          => $this->getCategories()
            , "store_url"           => $this->getStoreUrl()
            , "icon_url"            => $this->getIconUrl()
            , "avg_rating"          => $this->getAvgRating()
            , "minimum_os_version"  => $this->getMinimumOsVersion()
            , "supported_devices"   => $this->getSupportedDevices()
            , "delete_ts"           => $this->getDeleteTs()
            , "last_user_update_ts" => $this->getLastUserUpdateTs()
            , "create_ts"           => $this->getCreateTs()
            , "price_currency"      => $this->getPriceCurrency()
            , "version"             => $this->getVersion()
            , "developer"           => $this->getDeveloper()
            , "developer_website"   => $this->getDeveloperWebsite()
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
     * @return array
     */
    public function getUrls(): array {
        return $this->urls;
    }

    /**
     * @param array $urls
     */
    public function setUrls(array $urls): void {
        $this->urls = $urls;
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
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getRating(): float {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating(float $rating): void {
        $this->rating = $rating;
    }

    /**
     * @return int
     */
    public function getOperatingSystem(): int {
        return $this->operatingSystem;
    }

    /**
     * @param int $operatingSystem
     */
    public function setOperatingSystem(int $operatingSystem): void {
        $this->operatingSystem = $operatingSystem;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getStoreId(): string {
        return $this->storeId;
    }

    /**
     * @param string $storeId
     */
    public function setStoreId(string $storeId): void {
        $this->storeId = $storeId;
    }

    /**
     * @return DateTime
     */
    public function getLastUpdated(): DateTime {
        return $this->lastUpdated;
    }

    /**
     * @param DateTime $lastUpdated
     */
    public function setLastUpdated(DateTime $lastUpdated): void {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @return DateTime|null
     */
    public function getReleaseDate(): ?DateTime {
        return $this->releaseDate;
    }

    /**
     * @param DateTime|null $releaseDate
     */
    public function setReleaseDate(?DateTime $releaseDate): void {
        $this->releaseDate = $releaseDate;
    }

    /**
     * @return float
     */
    public function getUsage(): float {
        return $this->usage;
    }

    /**
     * @param float $usage
     */
    public function setUsage(float $usage): void {
        $this->usage = $usage;
    }

    /**
     * @return float
     */
    public function getResultsQuality(): float {
        return $this->resultsQuality;
    }

    /**
     * @param float $resultsQuality
     */
    public function setResultsQuality(float $resultsQuality): void {
        $this->resultsQuality = $resultsQuality;
    }

    /**
     * @return float
     */
    public function getPresentability(): float {
        return $this->presentability;
    }

    /**
     * @param float $presentability
     */
    public function setPresentability(float $presentability): void {
        $this->presentability = $presentability;
    }

    /**
     * @return string
     */
    public function getDidacticComment(): string {
        return $this->didacticComment;
    }

    /**
     * @param string $didacticComment
     */
    public function setDidacticComment(string $didacticComment): void {
        $this->didacticComment = $didacticComment;
    }

    /**
     * @return string
     */
    public function getDidacticRemark(): string {
        return $this->didacticRemark;
    }

    /**
     * @param string $didacticRemark
     */
    public function setDidacticRemark(string $didacticRemark): void {
        $this->didacticRemark = $didacticRemark;
    }

    /**
     * @return int
     */
    public function getPrivacy(): int {
        return $this->privacy;
    }

    /**
     * @param int $privacy
     */
    public function setPrivacy(int $privacy): void {
        $this->privacy = $privacy;
    }

    /**
     * @return string
     */
    public function getPrivacyComment(): string {
        return $this->privacyComment;
    }

    /**
     * @param string $privacyComment
     */
    public function setPrivacyComment(string $privacyComment): void {
        $this->privacyComment = $privacyComment;
    }

    /**
     * @return int
     */
    public function getAuthor(): int {
        return $this->author;
    }

    /**
     * @param int $author
     */
    public function setAuthor(int $author): void {
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getRecommendation(): int {
        return $this->recommendation;
    }

    /**
     * @param int $recommendation
     */
    public function setRecommendation(int $recommendation): void {
        $this->recommendation = $recommendation;
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
     * @return array
     */
    public function getTags(): array {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getCategories(): array {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories(array $categories): void {
        $this->categories = $categories;
    }

    /**
     * @return string
     */
    public function getStoreUrl(): string {
        return $this->storeUrl;
    }

    /**
     * @param string $storeUrl
     */
    public function setStoreUrl(string $storeUrl): void {
        $this->storeUrl = $storeUrl;
    }

    /**
     * @return string
     */
    public function getIconUrl(): string {
        return $this->iconUrl;
    }

    /**
     * @param string $iconUrl
     */
    public function setIconUrl(string $iconUrl): void {
        $this->iconUrl = $iconUrl;
    }

    /**
     * @return float
     */
    public function getAvgRating(): float {
        return $this->avgRating;
    }

    /**
     * @param float $avgRating
     */
    public function setAvgRating(float $avgRating): void {
        $this->avgRating = $avgRating;
    }

    /**
     * @return string
     */
    public function getMinimumOsVersion(): string {
        return $this->minimumOsVersion;
    }

    /**
     * @param string $minimumOsVersion
     */
    public function setMinimumOsVersion(string $minimumOsVersion): void {
        $this->minimumOsVersion = $minimumOsVersion;
    }

    /**
     * @return array
     */
    public function getSupportedDevices(): array {
        return $this->supportedDevices;
    }

    /**
     * @param array $supportedDevices
     */
    public function setSupportedDevices(array $supportedDevices): void {
        $this->supportedDevices = $supportedDevices;
    }

    /**
     * @return DateTime|null
     */
    public function getDeleteTs(): ?DateTime {
        return $this->deleteTs;
    }

    /**
     * @param DateTime|null $deleteTs
     */
    public function setDeleteTs(?DateTime $deleteTs): void {
        $this->deleteTs = $deleteTs;
    }

    /**
     * @return DateTime|null
     */
    public function getLastUserUpdateTs(): ?DateTime {
        return $this->lastUserUpdateTs;
    }

    /**
     * @param DateTime|null $lastUserUpdateTs
     */
    public function setLastUserUpdateTs(?DateTime $lastUserUpdateTs): void {
        $this->lastUserUpdateTs = $lastUserUpdateTs;
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
     * @return string
     */
    public function getPriceCurrency(): string {
        return $this->priceCurrency;
    }

    /**
     * @param string $priceCurrency
     */
    public function setPriceCurrency(string $priceCurrency): void {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @return string
     */
    public function getVersion(): string {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getDeveloper(): string {
        return $this->developer;
    }

    /**
     * @param string $developer
     */
    public function setDeveloper(string $developer): void {
        $this->developer = $developer;
    }

    /**
     * @return string
     */
    public function getDeveloperWebsite(): string {
        return $this->developerWebsite;
    }

    /**
     * @param string $developerWebsite
     */
    public function setDeveloperWebsite(string $developerWebsite): void {
        $this->developerWebsite = $developerWebsite;
    }

    /**
     * @return string|null
     */
    public function getIosPrivacy(): ?string {
        return $this->iosPrivacy;
    }

    /**
     * @param string|null $iosPrivacy
     */
    public function setIosPrivacy(?string $iosPrivacy): void {
        $this->iosPrivacy = $iosPrivacy;
    }

    /**
     * @return string
     */
    public function getPublisherEmail(): string {
        return $this->publisherEmail;
    }

    /**
     * @param string $publisherEmail
     */
    public function setPublisherEmail(string $publisherEmail): void {
        $this->publisherEmail = $publisherEmail;
    }

    public function isAndroid(): bool {
        return $this->getOperatingSystem() === App::ANDROID;
    }

    public function isIos(): bool {
        return $this->getOperatingSystem() === App::IOS;
    }


}
