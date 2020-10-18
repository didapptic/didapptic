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
 * Class File
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class File implements JsonSerializable {

    public const FILE_SIZE_BYTE_IN_KILOBYTE = 1024;
    public const FILE_SIZE_BYTE_IN_MEGABYTE = 1048576;
    public const FILE_SIZE_BYTE_IN_GIGABYTE = 1073741824;

    /** @var int|null $id */
    private $id;
    /** @var string $name */
    private $name;
    /** @var string $mimeType */
    private $mimeType;
    /** @var string $hash */
    private $hash;
    /** @var string $path */
    private $path;
    /** @var int $creatorId */
    private $creatorId;
    /** @var int $createTs */
    private $createTs;
    /** @var string|null $content */
    private $content;
    /** @var int $size */
    private $size = 0;

    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
        return
            [
                "id"               => $this->getId()
                , "name"           => $this->getName()
                , "mime_type"      => $this->getMimeType()
                , "hash"           => $this->getHash()
                , "path"           => $this->getPath()
                , "creator_id"     => $this->getCreatorId()
                , "create_ts"      => $this->getCreateTs()
                , "content"        => $this->getContent()
                , "formatted_size" => $this->getFormattedSize()
                , "size"           => $this->getSize()
            ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
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
    public function getMimeType(): string {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType(string $mimeType): void {
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getHash(): string {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void {
        $this->path = $path;
    }

    /**
     * @return int
     */
    public function getCreatorId(): int {
        return $this->creatorId;
    }

    /**
     * @param int $creatorId
     */
    public function setCreatorId(int $creatorId): void {
        $this->creatorId = $creatorId;
    }

    /**
     * @return int
     */
    public function getCreateTs(): int {
        return $this->createTs;
    }

    /**
     * @param int $createTs
     */
    public function setCreateTs(int $createTs): void {
        $this->createTs = $createTs;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function getFormattedSize(): string {

        if (0 === $this->getSize()) {
            return '0 B';
        } else if ($this->getSize() < self::FILE_SIZE_BYTE_IN_KILOBYTE) {
            return round($this->getSize(), 2) . " B";
        } else if ($this->getSize() < self::FILE_SIZE_BYTE_IN_MEGABYTE) {
            return round($this->getSize() / self::FILE_SIZE_BYTE_IN_KILOBYTE, 2) . " KB";
        } else if ($this->getSize() < self::FILE_SIZE_BYTE_IN_GIGABYTE) {
            return round($this->getSize() / self::FILE_SIZE_BYTE_IN_MEGABYTE, 2) . " MB";
        } else {
            return round($this->getSize() / self::FILE_SIZE_BYTE_IN_GIGABYTE, 2) . " GB";
        }

    }

    public function getSize(): int {
        return $this->size;
    }

    public function setSize(int $size): void {
        $this->size = $size;
    }


}
