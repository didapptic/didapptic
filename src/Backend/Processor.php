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

/**
 * Class Processor
 *
 * @package Didapptic\Backend
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Processor {

    /** @var Fetcher */
    private $fetcher;
    /** @var Decoder */
    private $decoder;

    /**
     * Processor constructor.
     *
     * @param Fetcher $fetcher
     * @param Decoder $decoder
     */
    public function __construct(
        Fetcher $fetcher
        , Decoder $decoder
    ) {
        $this->fetcher = $fetcher;
        $this->decoder = $decoder;
    }

    /**
     * @param string $endpoint
     *
     * @return array|null
     */
    public function get(string $endpoint): ?array {
        return $this->decoder->decode(
            $this->fetcher->get($endpoint)
        );
    }

    /**
     * @param string $endpoint
     *
     * @return array|null
     */
    public function delete(string $endpoint): ?array {
        return $this->decoder->decode(
            $this->fetcher->delete($endpoint)
        );
    }

    /**
     * @param string $endpoint
     * @param array  $parameters
     *
     * @return array|null
     */
    public function put(string $endpoint, array $parameters): ?array {
        return $this->decoder->decode(
            $this->fetcher->put($endpoint, $parameters)
        );
    }

    /**
     * @param string $endpoint
     * @param array  $parameters
     *
     * @return array|null
     */
    public function post(string $endpoint, array $parameters): ?array {
        return $this->decoder->decode(
            $this->fetcher->post($endpoint, $parameters)
        );
    }

}
