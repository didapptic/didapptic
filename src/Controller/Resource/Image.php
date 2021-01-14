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

use Didapptic\Didapptic;
use Didapptic\Object\Constant\HTTP;
use Didapptic\Object\Constant\MimeType;
use Didapptic\Object\Environment;
use doganoo\PHPUtil\FileSystem\DirHandler;
use doganoo\PHPUtil\Log\FileLogger;

/**
 * Class Image
 *
 * @package Didapptic\Controller\Resource
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Image extends AbstractResource {

    /** @var string */
    private $name;

    /** @var Environment */
    private $environment;

    /** @var DirHandler */
    private $dirHandler;

    public function __construct(
        Environment $environment
        , DirHandler $dirHandler
    ) {
        parent::__construct();
        $this->environment = $environment;
        $this->dirHandler  = $dirHandler;
        $this->setMimeType(MimeType::JPEG);
    }

    protected function onCreate(): void {
        $name = $this->getArgument("name");
        $this->name = null !== $name ? $name : '';
    }

    protected function create(): ?string {

        $path = Didapptic::getServer()->getImageRoot();
        $this->dirHandler->setPath($path);
        $fileHandler = $this->dirHandler->findFile($this->name);

        if (null === $fileHandler) return null;
        if (false === $fileHandler->isFile()) {
            FileLogger::debug("can not find {$fileHandler->getPath()}, regarding to {$this->name}");
            $this->setResponseCode(HTTP::NOT_FOUND);
            return null;
        }

        return $fileHandler->getContent();
    }

    protected function onDestroy(): void {

    }

}
