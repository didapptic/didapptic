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

namespace Didapptic\Service\Session;

use doganoo\PHPUtil\HTTP\Session;

/**
 * Class SessionService
 *
 * @package Didapptic\Service\Session
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class SessionService {

    public const SESSION_GC_MAX_LIFETIME_NAME = "session.gc_maxlifetime";
    public const SESSION_GC_MAX_LIFETIME      = 3600;

    /** @var Session */
    private $session;

    public function __construct(Session $session) {
        $this->session = $session;
        ini_set(
            SessionService::SESSION_GC_MAX_LIFETIME_NAME
            , (string) SessionService::SESSION_GC_MAX_LIFETIME
        );
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function set(string $name, string $value): bool {
        $this->session->start();
        $this->session->set($name, $value);
        return true;
    }

    /**
     * @param string      $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function get(string $name, ?string $default = null): ?string {
        $this->session->start();
        return $this->session->get($name, $default);
    }

    public function killAll(): void {
        $this->session->start();
        $this->destroy();
    }

    public function destroy(): void {
        $this->session->start();
        $this->session->destroy();
    }

    public function getAll(): array {
        $this->session->start();
        return $this->session->getAll();
    }

    /**
     * @param string $name
     */
    public function kill(string $name): void {
        $this->session->start();
        $this->session->remove($name);
    }

}
