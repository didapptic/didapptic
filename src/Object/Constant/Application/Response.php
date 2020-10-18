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

namespace Didapptic\Object\Constant\Application;

class Response {

    public const OK                       = 1000;
    public const FAILED                   = 2000;
    public const NO_USER                  = 2001;
    public const RESOURCE_EXISTS          = 2002;
    public const INVALID_PUT_DATA         = 2003;
    public const EXCEPTION_OCCURED        = 2004;
    public const DATABASE_EXCEPTION       = 2005;
    public const RESOURCE_REACTIVATED     = 2006;
    public const APP_MONSTA_LIMIT_REACHED = 3100;

    public const FIELD_NAME_RESPONSE_CODE = "response_code";
    public const FIELD_NAME_DATA          = "data";

    private function __construct() {

    }

    private function __clone() {

    }

}
