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

namespace Didapptic\Controller\Resource;

use Didapptic\Object\Constant\HTTP;
use Didapptic\Repository\FileRepository;
use Didapptic\Service\Session\SessionService;

/**
 * Class File
 *
 * @package Didapptic\Controller\Resource
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class File extends AbstractResource {

    /** @var FileRepository */
    private $fileManager;
    /** @var SessionService */
    private $sessionService;

    public function __construct(
        FileRepository $fileManager
        , SessionService $sessionService
    ) {
        parent::__construct();
        $this->fileManager    = $fileManager;
        $this->sessionService = $sessionService;
    }

    protected function onCreate(): void {

    }

    protected function create(): ?string {
        $id    = $this->getArgument("id");
        $token = $this->getArgument("token");
        $file  = $this->fileManager->getById((int) $id);

        if (null === $token || "" === $token) {
            $this->setResponseCode(HTTP::UNAUTHORIZED);
            return null;
        }
        $sessionToken = $this->sessionService->get((string) $token, null);

        if (null === $sessionToken || "" === $sessionToken || "true" !== $sessionToken) {
            $this->setResponseCode(HTTP::UNAUTHORIZED);
            return null;
        }

        if (null === $file) {
            $this->setResponseCode(HTTP::NOT_FOUND);
            return null;
        }

        $path = $file->getPath();

        if (false === is_file($path)) {
            $this->setResponseCode(HTTP::NOT_FOUND);
            return null;
        }

        $mimeType = $file->getMimeType();

        if ("" === $mimeType) {
            $this->setResponseCode(HTTP::INTERNAL_SERVER_ERROR);
            return null;
        }

        $this->setMimeType($mimeType);

        return file_get_contents($path);
    }

    protected function onDestroy(): void {

    }

}
