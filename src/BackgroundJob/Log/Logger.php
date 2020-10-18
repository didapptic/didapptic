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

namespace Didapptic\BackgroundJob\Log;

use doganoo\Backgrounder\Service\Log\ILoggerService;
use doganoo\PHPUtil\Log\FileLogger;

/**
 * Class Logger
 *
 * @package Didapptic\BackgroundJob\Log
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Logger implements ILoggerService {

    /**
     * @param string $key
     * @param string $message
     * @param int    $level
     */
    public function log(string $key, string $message, int $level): void {

        $message = json_encode([
            "key"       => $key
            , "message" => $message
        ]);

        switch ($level) {
            case ILoggerService::DEBUG:
                FileLogger::debug($message);
                break;
            case ILoggerService::INFO:
                FileLogger::info($message);
                break;
            case ILoggerService::WARN:
                FileLogger::warn($message);
                break;
            case ILoggerService::ERROR:
                FileLogger::error($message);
                break;
            case ILoggerService::FATAL:
                FileLogger::fatal($message);
                break;
            case ILoggerService::TRACE:
                FileLogger::trace($message);
                break;
            default:
                FileLogger::trace($message);
        }
    }

}
