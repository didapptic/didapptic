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

namespace Didapptic\BackgroundJob\Task;

use Didapptic\Object\Token;
use Didapptic\Repository\TokenRepository;
use doganoo\Backgrounder\Task\Task;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;
use Exception;

/**
 * Class TokenCleanUp
 *
 * @package Didapptic\BackgroundJob\Task
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class TokenCleanUp extends Task {

    /** @var TokenRepository */
    private $tokenManager;
    /** @var ArrayList|null */
    private $tokenList;
    /** @var int */
    private $deactivated = 0;

    public function __construct(TokenRepository $tokenManager) {
        $this->tokenManager = $tokenManager;
    }

    protected function onAction(): void {
        $this->tokenList = $this->tokenManager->getOutdatedTokens();
    }

    /**
     * @throws Exception
     */
    protected function action(): bool {
        if (null === $this->tokenList) return false;

        /**
         * @var int   $key
         * @var Token $token
         */
        foreach ($this->tokenList as $key => $token) {
            $this->tokenManager->deactivate($token->getToken());
            $this->deactivated++;
        }
        return true;
    }


    protected function onClose(): void {
        FileLogger::info("number of deactivated tokens: {$this->deactivated}");
    }

}
