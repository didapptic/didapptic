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

namespace Didapptic\Backend;

use Didapptic\Didapptic;
use Didapptic\Object\Environment;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Class Fetcher
 *
 * @package Didapptic\Backend
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Fetcher {

    /** @var Client */
    private $client;

    /** @var Environment */
    private $environment;

    /**
     * Fetcher constructor.
     *
     * @param Client      $client
     * @param Environment $environment
     */
    public function __construct(
        Client $client
        , Environment $environment
    ) {
        $this->client      = $client;
        $this->environment = $environment;
    }

    /**
     * @param string $endpoint
     *
     * @return string
     */
    public function get(string $endpoint): string {
        $url = Didapptic::getBaseURL(true) . "/" . $endpoint;
        /** @var Response $response */
        $response = $this->client->get($url);
        return (string) $response->getBody();
    }

    /**
     * @param string $endpoint
     *
     * @return string
     */
    public function delete(string $endpoint): string {
        $url = Didapptic::getBaseURL(true) . "/" . $endpoint;
        /** @var Response $response */
        $response = $this->client->delete($url);
        return (string) $response->getBody();
    }

    /**
     * @param string $endpoint
     * @param array  $parameters
     *
     * @return string
     */
    public function put(string $endpoint, array $parameters): string {
        $url = Didapptic::getBaseURL(true) . "/" . $endpoint;
        /** @var Response $response */
        $response = $this->client->put(
            $url
            , [
                "form_params" => $parameters
            ]
        );
        return (string) $response->getBody();
    }

    /**
     * @param string $endpoint
     * @param array  $parameters
     *
     * @return string
     */
    public function post(string $endpoint, array $parameters): string {
        $url = Didapptic::getBaseURL(true) . "/" . $endpoint;
        /** @var Response $response */
        $response = $this->client->post(
            $url
            , [
                "form_params" => $parameters
            ]
        );
        return (string) $response->getBody();
    }

}
