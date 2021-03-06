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
use Didapptic\Manager\Strings\FrontendManager;
use Didapptic\Object\Constant\MimeType;
use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;

/**
 * Class Strings
 *
 * @package Didapptic\Controller\Resource
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Strings extends AbstractResource {

    /** @var FrontendManager */
    private $frontendManager;

    public function __construct(FrontendManager $frontendManager) {
        parent::__construct();
        $this->frontendManager = $frontendManager;
    }

    protected function onCreate(): void {
        $this->frontendManager->addPath(
            Didapptic::getServer()->getFrontendStringPath()
        );
    }

    protected function create(): ?string {
        $templates = $this->hashTableToArray(
            $this->frontendManager->getAll()
        );
        $this->setMimeType(MimeType::JSON);
        $json = json_encode($templates);
        return false === $json ? null : $json;
    }

    private function hashTableToArray(HashMap $map): array {
        $array = [];
        foreach ($map->keySet() as $key) {
            $name         = basename($key);
            $name         = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
            $array[$name] = $map->get($key);
        }
        return $array;
    }

    protected function onDestroy(): void {

    }

}
