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

namespace Didapptic\Submit;

use Didapptic\Backend\Processor;
use Didapptic\Didapptic;

/**
 * Class AbstractSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
abstract class AbstractSubmit {

    /** @var array */
    private $arguments;
    /** @var Processor */
    private $processor;
    /** @var array */
    private $response;

    /**
     * AbstractSubmit constructor.
     */
    public function __construct() {
        $this->processor = Didapptic::getServer()->query(Processor::class);
        $this->response  = [];
    }

    public function run(): bool {
        if (false === $this->valid()) return false;
        $this->onCreate();
        $performed = $this->create();
        $this->onDestroy();
        return $performed;
    }

    protected abstract function valid(): bool;

    protected abstract function onCreate(): void;

    protected abstract function create(): bool;

    protected abstract function onDestroy(): void;

    public function getArgument(string $name): ?string {
        $value = $this->arguments[$name] ?? null;
        if (null === $value) return null;
        return (string) $value;
    }

    public function getArguments(): array {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void {
        $this->arguments = $arguments;
    }

    public function addResponse(string $key, $value): void {
        $this->response[$key] = $value;
    }

    public function getResponse(): array {
        return $this->response;
    }

    public function setResponse(array $response): void {
        $this->response = $response;
    }

    protected function getProcessor(): Processor {
        return $this->processor;
    }

}
