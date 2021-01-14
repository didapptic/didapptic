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

namespace Didapptic\Service\Application\WordPress;

use Didapptic\Didapptic;
use Didapptic\Object\Constant\HTTP;
use Didapptic\Object\Environment;
use Didapptic\Object\Registrant;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class WordPressService
 *
 * @package Didapptic\Service
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class WordPressService {

    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment) {
        $this->environment = $environment;
    }

    public function createUser(Registrant $registrant): ?array {
        // do not create users for WP if we are not on production!
        if (false === $this->environment->isProduction()) return [];

        $url        = $this->environment->getWordPressURL() . "/wp-json/wp/v2/users";
        $parameters =
            [
                "username"      => $registrant->getName()
                , "name"        => $registrant->getName()
                , "first_name"  => $registrant->getFirstName()
                , "last_name"   => $registrant->getLastName()
                , "email"       => $registrant->getEmail()
                , "url"         => $registrant->getWebsiteURL()
                , "description" => "created by didapptic, for creating blog posts"
                , "locale"      => "de_DE"
                , "nickname"    => $registrant->getName()
                , "slug"        => $registrant->getName()
                , "roles"       => ["author"]
                , "password"    => $registrant->getPlainPassword()
            ];

        $request = $this->post($url, $parameters);

        if (null === $request) return null;

        if (false === HTTP::isTwoHundred((int) $request->getStatusCode())) return null;
        return json_decode((string) $request->getBody(), true);
    }

    private function post(string $url, array $parameters): ?ResponseInterface {
        // do not create users for WP if we are not on production!
        if (false === $this->environment->isProduction()) return null;
        /** @var Client $client */
        $client = Didapptic::getServer()->query(Client::class);
        return $client->post($url, [
            "headers"       => [
                "Authorization" => "Basic {$this->environment->getWPApplicationPassword()}"
            ]
            , "form_params" => $parameters
        ]);
    }

    public function updateUser(int $id, string $password): bool {
        $request = $this->post(
            $this->environment->getWordPressURL() . "/wp-json/wp/v2/users/$id"
            , [
                "password" => $password
            ]
        );

        if (null === $request) return false;

        return HTTP::isTwoHundred((int) $request->getStatusCode());
    }

}
