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
use Didapptic\Object\Environment;
use doganoo\PHPUtil\Storage\PDOConnector;

require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . "/../../src/Didapptic.php";

$didapptic = new Didapptic();
/** @var Environment $environment */
$environment = Didapptic::getServer()->query(Environment::class);
/** @var PDOConnector $connector */
$connector = Didapptic::getServer()->query(PDOConnector::class);

$properties = $environment->getDatabaseProperties();
$connector->connect();
$pdo = $connector->getConnection();

return [
    'environments' => [
        'default_database' => 'development'
        , 'development'    => [
            'name'         => $properties["dbname"]
            , 'connection' => $pdo
        ]
        , 'production'     => [
            'name'         => $properties["dbname"]
            , 'connection' => $pdo
        ]
        , 'testing'        => [
            'name'         => $properties["dbname"]
            , 'connection' => $pdo
        ]
    ]
    , "paths"      => [
        "migrations" => [
            Didapptic::getAppRoot() . "/src/Repository/Migration"
        ]
    ]
];
