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

namespace Didapptic\Service\Material;

use DateTime;
use Didapptic\Didapptic;
use Didapptic\Object\File;
use Didapptic\Object\Material;
use Didapptic\Repository\MaterialRepository;
use Didapptic\Service\File\Mime\MimeService;
use doganoo\PHPUtil\FileSystem\DirHandler;
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Util\DateTimeUtil;
use Exception;
use SplFileInfo;

/**
 * Class MaterialService
 *
 * @package Didapptic\Service
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MaterialService {

    /** @var MaterialRepository */
    private $materialManager;
    /** @var MimeService */
    private $mimeService;

    public function __construct(
        MaterialRepository $materialManager
        , MimeService $mimeService
    ) {
        $this->materialManager = $materialManager;
        $this->mimeService     = $mimeService;
    }

    public function remove(Material $material): bool {

        if (null === $material->getId()) {
            throw new Exception('no material id to delete');
        }

        $deleted = $this->materialManager->delete($material->getId());

        if (false === $deleted) return false;

        $deletedAll = true;
        $files      = $material->getFiles();

        if (null !== $files) {
            /** @var File $file */
            foreach ($files as $file) {
                $path       = $file->getPath();
                $deleted    = @unlink($path);
                $deletedAll = $deletedAll && $deleted;
            }
        }

        if (false === $deletedAll) return false;
        return true;
    }

    public function verifyPassword(Material $material, string $password): bool {
        return password_verify($password, (string) $material->getPassword());
    }

    public function upload(array $parameters): bool {

        $date        = $parameters['date'] ?? null;
        $description = $parameters["description"] ?? null;
        $files       = $parameters["files"] ?? null;
        $user        = $parameters["user_id"] ?? null;
        $name        = $parameters["name"] ?? null;
        $password    = $parameters["password"] ?? null;

        $put = false;

        if (
            null === $date ||
            null === $description ||
            null === $files ||
            null === $user ||
            null === $name
        ) {
            return false;
        }

        if (
            false === DateTimeUtil::valid($date, "d.m.Y") ||
            "" === $description ||
            "" === $name ||
            0 === count($files) ||
            0 === (int) $user
        ) {
            return false;
        }

        $password = (null !== $password) ?
            password_hash($password, PASSWORD_BCRYPT) :
            null;

        $material = new Material();
        $material->setDate(strtotime($date));
        $material->setDescription($description);
        $material->setCreatorId($user);
        $material->setCreateTs(DateTimeUtil::getUnixTimestamp());
        $material->setName($name);
        $material->setPassword((string) $password);

        foreach ($files as $key => $file) {
            $filePath    = $file["file_path"] ?? "";
            $splFileInfo = new SplFileInfo($filePath);

            $extension = $splFileInfo->getExtension();
            $mimeType  = mime_content_type((string) $splFileInfo->getRealPath());
            $mimes     = $this->mimeService->getSupportedMimeTypes();

            foreach ($mimes as $extension => $mime) {
                if (false === in_array($mimeType, $mime)) {
                    FileLogger::debug($file["name"] . " is not allowed. Skipping");
                    continue;
                }
            }

            $path     = $this->getMaterialUploadPath();
            $path     = $path . "/" . md5(uniqid()) . "." . $extension;
            $content  = file_get_contents((string) $splFileInfo->getRealPath());
            $fileSize = filesize((string) $splFileInfo->getRealPath());

            $__file = new File();
            $__file->setCreateTs(DateTimeUtil::getUnixTimestamp());
            $__file->setCreatorId($user);
            $__file->setName($file["name"]);
            $__file->setPath($path);
            $__file->setHash((string) md5((string) $content));
            $__file->setMimeType((string) $mimeType);
            $__file->setContent((string) $content);
            $__file->setSize((int) $fileSize);

            $material->addFile($__file);

            unlink((string) $splFileInfo->getRealPath());
        }

        $lastId = $this->materialManager->insert($material);
        if (null === $lastId) {
            return false;
        }

        /** @var File $file */
        foreach ($material->getFiles() ?? [] as $file) {
            $put &= @file_put_contents($file->getPath(), $file->getContent());
        }

        return true;
    }

    public function getMaterialUploadPath(): string {
        $path       = Didapptic::getServer()->getMaterialPath();
        $dateTime   = new DateTime();
        $path       = $path . "/" . $dateTime->format("Y") . "/" . $dateTime->format("d");
        $dirHandler = new DirHandler($path);
        if (!$dirHandler->isDir()) $dirHandler->mkdir();
        return (string) $dirHandler->toRealPath();
    }

}
