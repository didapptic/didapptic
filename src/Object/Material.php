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

use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use JsonSerializable;

/**
 * Class Material
 *
 * @package Didapptic\Object
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Material implements JsonSerializable {

    /** @var int|null $id */
    private $id;
    /** @var string */
    private $description;
    /** @var int $date */
    private $date;
    /** @var int $creatorId */
    private $creatorId;
    /** @var int $createTs */
    private $createTs;
    /** @var null|ArrayList */
    private $files = null;
    /** @var string $name */
    private $name;
    /** @var null|string $password */
    private $password;

    public function addFile(File $file): void {
        if (null === $this->files) {
            $this->files = new ArrayList();
        }
        $this->files->add($file);
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
                "id"            => $this->getId()
                , "description" => $this->getDescription()
                , "date"        => $this->getDate()
                , "files"       => $this->getFiles()
                , "creator_id"  => $this->getCreatorId()
                , "create_ts"   => $this->getCreateTs()
                , "name"        => $this->getName()
                , "password"    => $this->getPassword()
            ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void {
        $this->id = $id;
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
     * @return int
     */
    public function getDate(): int {
        return $this->date;
    }

    /**
     * @param int $date
     */
    public function setDate(int $date): void {
        $this->date = $date;
    }

    public function getFiles(): ?ArrayList {
        return $this->files;
    }

    public function setFiles(ArrayList $files): void {
        $this->files = $files;
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

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): void {
        $this->password = $password;
    }

}
