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

namespace Didapptic\Service\Slim\Response;

use Didapptic\Didapptic;
use Didapptic\Object\Constant\HTTP;
use Didapptic\Object\Constant\Response as FrontendResponse;
use Slim\Http\Response;

/**
 * Class ResponseService
 *
 * @package Didapptic\Service\Slim\Response
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class ResponseService {

    public function getSubmitUnauthorizedResponse(Response $response): Response {
        $response = $response->withStatus(HTTP::UNAUTHORIZED);
        $response->getBody()
            ->write(
                (string) json_encode(
                    [
                        FrontendResponse::FIELD_NAME_RESPONSE_CODE => FrontendResponse::OK
                        , FrontendResponse::FIELD_NAME_CONTENT     => [
                        "unauthorized. Please provide credentials"
                    ]
                    ]
                )
            );
        return $response;
    }

    public function getControllerUnauthorizedResponse(Response $response): Response {
        $response = $response->withStatus(HTTP::MOVED_TEMPORARILY);
        return $response->withRedirect(Didapptic::getBaseURL(true) . "/menu/login/?expired=true");
    }

}
