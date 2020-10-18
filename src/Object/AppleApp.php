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

/**
 * Class AppleApp
 *
 * @package    Didapptic\Object
 * @author     Dogan Ucar <dogan@dogan-ucar.de>
 * @deprecated use App instead, set default values
 */
class AppleApp extends App {

    private $gameCenterEnabled  = false;
    private $itunesDeveloperUrl = "";
    private $kind               = "";
    private $features           = "";
    private $languageCode       = "";
    private $fileSize           = 0; //bytes
    private $wrapperType        = "";
    private $releaseNotes       = "";
    /** @var array */
    private $genres;

    /**
     * @return bool
     */
    public function isGameCenterEnabled(): bool {
        return $this->gameCenterEnabled;
    }

    /**
     * @param bool $gameCenterEnabled
     */
    public function setGameCenterEnabled(bool $gameCenterEnabled): void {
        $this->gameCenterEnabled = $gameCenterEnabled;
    }

    /**
     * @return string
     */
    public function getItunesDeveloperUrl(): string {
        return $this->itunesDeveloperUrl;
    }

    /**
     * @param string $itunesDeveloperUrl
     */
    public function setItunesDeveloperUrl(string $itunesDeveloperUrl): void {
        $this->itunesDeveloperUrl = $itunesDeveloperUrl;
    }

    /**
     * @return string
     */
    public function getKind(): string {
        return $this->kind;
    }

    /**
     * @param string $kind
     */
    public function setKind(string $kind): void {
        $this->kind = $kind;
    }

    /**
     * @return string
     */
    public function getFeatures(): string {
        return $this->features;
    }

    /**
     * @param string $features
     */
    public function setFeatures(string $features): void {
        $this->features = $features;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string {
        return $this->languageCode;
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode(string $languageCode): void {
        $this->languageCode = $languageCode;
    }

    /**
     * @return int
     */
    public function getFileSize(): int {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize(int $fileSize): void {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string
     */
    public function getWrapperType(): string {
        return $this->wrapperType;
    }

    /**
     * @param string $wrapperType
     */
    public function setWrapperType(string $wrapperType): void {
        $this->wrapperType = $wrapperType;
    }

    /**
     * @return string
     */
    public function getReleaseNotes(): string {
        return $this->releaseNotes;
    }

    /**
     * @param string $releaseNotes
     */
    public function setReleaseNotes(string $releaseNotes): void {
        $this->releaseNotes = $releaseNotes;
    }

    public function jsonSerialize() {
        return parent::jsonSerialize() +
            [
                "game_center_enabled"    => $this->gameCenterEnabled
                , "itunes_developer_url" => $this->itunesDeveloperUrl
                , "kind"                 => $this->kind
                , "features"             => $this->features
                , "language_code"        => $this->languageCode
                , "file_size"            => $this->fileSize
                , "wrapper_type"         => $this->wrapperType
                , "release_notes"        => $this->releaseNotes
                , "genres"               => $this->getGenres()
            ];
    }

    /**
     * @return array
     */
    public function getGenres(): array {
        return $this->genres;
    }

    /**
     * @param array $genres
     */
    public function setGenres(array $genres): void {
        $this->genres = $genres;
    }

}
