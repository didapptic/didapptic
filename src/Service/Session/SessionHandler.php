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

use Didapptic\Manager\SessionManager;
use SessionHandlerInterface;

/**
 * Class SessionHandler
 *
 * @package Didapptic\Service\Session
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class SessionHandler implements SessionHandlerInterface {

    /** @var SessionManager */
    private $sessionManager;

    public function __construct(SessionManager $sessionManager) {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @inheritDoc
     */
    public function open($save_path, $name) {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function read($session_id) {
        return (string) $this->sessionManager->get($session_id);
    }

    /**
     * @inheritDoc
     */
    public function write($session_id, $session_data) {
        return $this->sessionManager->replace($session_id, $session_data);
    }

    /**
     * @inheritDoc
     */
    public function gc($maxlifetime) {
        return $this->sessionManager->deleteByLastUpdate($maxlifetime);
    }

    /**
     * @inheritDoc
     */
    public function destroy($session_id) {
        return $this->sessionManager->deleteById($session_id);
    }

    /**
     * @inheritDoc
     */
    public function close() {
        return true;
    }

}
