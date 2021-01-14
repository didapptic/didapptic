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

use Didapptic\Didapptic;
use Didapptic\Service\Asset\Less\Compiler;
use Didapptic\Service\Asset\Web\Fetcher\Fetcher;
use GuzzleHttp\Client;

//require_once "../src/Didapptic.php";
require_once "../vendor/autoload.php";

//$didapptic = new Didapptic();
//
///** @var Compiler $compiler */
//$compiler = Didapptic::getServer()->query(Compiler::class);
///** @var Fetcher $fetcher */
//$fetcher = Didapptic::getServer()->query(Fetcher::class);

$compiler = new Compiler();
$fetcher  = new Fetcher(
    new Client()
);
$compiler->compileAll();
$fetcher->load(
    [
        "https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css"
        , "https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css"
        , "https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css"
    ]
);
