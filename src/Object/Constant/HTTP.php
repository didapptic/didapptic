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

namespace Didapptic\Object\Constant;

/**
 * Class HTTP
 *
 * @package Didapptic\Object\Constant
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class HTTP {

    public const BAD_REQUEST           = 400;
    public const INTERNAL_SERVER_ERROR = 500;
    public const MULTIPLE_CHOICES      = 300;
    public const MOVED_TEMPORARILY     = 302;
    public const NOT_FOUND             = 404;
    public const OK                    = 200;
    public const UNAUTHORIZED          = 401;

    private function __construct() {
        // in order to have a class holding
        // constants only
    }

    public static function isTwoHundred(int $code): bool {
        return $code < HTTP::MULTIPLE_CHOICES && $code >= HTTP::OK;
    }

    public function __clone() {
        // check __construct()
    }

}
