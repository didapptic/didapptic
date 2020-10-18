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
use Didapptic\Exception\DidappticNotInstalledException;
use Didapptic\Object\Environment;
use doganoo\PHPUtil\Log\FileLogger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

require_once '../vendor/autoload.php';

header('Access-Control-Allow-Origin: *');

$environment = null;
$didapptic   = null;

$didapptic = new Didapptic();
$didapptic->setSessionHandler();

/** @var Environment $environment */

$environment = Didapptic::getServer()->query(Environment::class);
$app         = new App([
    'settings' => [
        'displayErrorDetails' => $environment->isDebug()
    ],
]);

$app->add(function (RequestInterface $request, ResponseInterface $response, $next) use ($didapptic) {
    $installed = $didapptic->isInstalled();
    if (!$installed) throw new DidappticNotInstalledException("Die Anwendung wurde nicht ordnungsgemäß installiert. Bitte kontaktieren Sie einen Administrator!", 1000);
    $response = $next($request, $response);
    return $response;
});

// HTTP DELETE
$app->delete("/v1/material/file/{id}/", \Didapptic\Action\Submit::class);
$app->delete("/v1/file/{id}/{materialId}/", \Didapptic\Action\Submit::class);
$app->delete("/v1/applications/delete/{appId}/", \Didapptic\Action\Submit::class);

// HTTP PUT
$app->put("/menu/new-user/new/submit/", \Didapptic\Action\Submit::class);
$app->put("/menu/password-lost/submit/", \Didapptic\Action\Submit::class);
$app->put("/v1/menu/contact/new/submit/", \Didapptic\Action\Submit::class);
$app->put("/menu/login/submit/", \Didapptic\Action\Submit::class);
$app->put("/v1/notification/update/", \Didapptic\Action\Submit::class);
$app->put("/menu/profile/add/", \Didapptic\Action\Submit::class);

// HTTP GET
$app->get("/menu/material/", \Didapptic\Action\Controller::class);
$app->get("/menu/hints/", \Didapptic\Action\Controller::class);
$app->get("/menu/about/", \Didapptic\Action\Controller::class);
$app->get("/menu/contact/", \Didapptic\Action\Controller::class);
$app->get("/menu/imprint/", \Didapptic\Action\Controller::class);
$app->get("/menu/privacy/", \Didapptic\Action\Controller::class);
$app->get("/menu/partner/", \Didapptic\Action\Controller::class);
$app->get("/menu/login/", \Didapptic\Action\Controller::class);
$app->get("/menu/settings/", \Didapptic\Action\Controller::class);
$app->get("/menu/profile/{userId}/", \Didapptic\Action\Controller::class)
    ->setName("profile");
$app->get("/menu/password-lost/", \Didapptic\Action\Controller::class);
$app->get("/menu/password-lost/{token}/", \Didapptic\Action\Controller::class);
$app->get("/", \Didapptic\Action\Controller::class)
    ->setName("home");
$app->get("/menu/new-app/", \Didapptic\Action\Controller::class);
$app->get("/menu/new-user/", \Didapptic\Action\Controller::class);
$app->get("/menu/logout/", \Didapptic\Action\Controller::class);
$app->get("/v1/app-modal-detail/{appId}/", \Didapptic\Action\Controller::class);
$app->get("/menu/edit-app/{storeId}/", \Didapptic\Action\Controller::class);
$app->get("/v1/all-apps/remaining-apps/{chunkSize}/", \Didapptic\Action\Submit::class);

// HTTP GET - RESOURCES
$app->get("/v1/resources/img/{name}/image/", \Didapptic\Action\Resource::class);
$app->get("/v1/material/file/{id}/{token}/", \Didapptic\Action\Resource::class);
$app->get("/templates/all/", \Didapptic\Action\Resource::class);
$app->get("/strings/all/", \Didapptic\Action\Resource::class);

// HTTP POST
$app->post("/v1/material/files/upload/", \Didapptic\Action\Submit::class);
$app->post("/v1/material/password/check/", \Didapptic\Action\Submit::class);
$app->post("/menu/update-app/update/submit/", \Didapptic\Action\Submit::class);
$app->post("/v1/resources/img/{name}/image/", \Didapptic\Action\Submit::class);
$app->post("/v1/subject/new/", \Didapptic\Action\Submit::class);
$app->post("/v1/category/new/", \Didapptic\Action\Submit::class);
$app->post("/v1/tag/new/", \Didapptic\Action\Submit::class);
$app->post("/menu/new-app/new/submit/", \Didapptic\Action\Submit::class);
$app->post("/password/update/", \Didapptic\Action\Submit::class);

try {
    $app->run();
} catch (Exception $e) {
    FileLogger::debug($e->getTraceAsString());
}
