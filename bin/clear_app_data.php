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
use Didapptic\Object\App;
use Didapptic\Repository\App\AppRepository;

require_once "../src/Didapptic.php";
require_once "../vendor/autoload.php";

$didapptic = new Didapptic();

/** @var AppRepository $appRepository */
$appRepository = Didapptic::getServer()->query(AppRepository::class);
$apps          = Didapptic::getServer()->getAppsFromCache();

/** @var App $app */
foreach ($apps as &$app) {
    $app->setCreateTs(new DateTime("2020-06-02"));
    $app->setStoreId(
        sanitizeStoreId($app->getStoreId())
    );
}

$appRepository->updateAll($apps);


function sanitizeStoreId(string $storeId): string {
    $delimiter = "&";
    if (false === strpos($storeId, $delimiter)) return $storeId;
    $data = explode($delimiter, $storeId);
//    if (false === $data) return $storeId;
    if (1 === count($data)) return $storeId;
    return (string) $data[0];
}
