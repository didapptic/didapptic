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

namespace Didapptic\Service\HTTP\URL;

/**
 * Class URLService
 *
 * @package Didapptic\Service\HTTP\URL
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class URLService {

    public function getParameterFromURL(string $url, string $parameter): ?string {
        $parts  = parse_url($url);
        $result = [];
        if (false === $parts) return null;
        $query = $parts["query"] ?? null;

        if (null === $query) return null;
        parse_str($query, $result);

        return $result[$parameter] ?? null;
    }

    public function regexParameterFromURL(string $url, string $pattern): ?string {
        $matches = [];
        $matched = preg_match($pattern, $url, $matches);
        // weird stuff:
        // returns false on error, 0 if pattern not found
        if (false === $matched || 0 === $matched) return null;

        // we are interested in the first found value ---> extend if needed
        // weird stuff v2:
        // $matches[0] will contain the text that matched the full pattern, $matches[1]
        // will have the text that matched the first captured parenthesized
        // subpattern, and so on.

        $fullPattern = $matches[0] ?? null;
        $value       = $matches[1] ?? null;
        if (null === $value || null === $fullPattern || $pattern !== $fullPattern) return null;
        return $value;
    }

    public function isURL(string $url): bool {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return false !== filter_var($url, FILTER_VALIDATE_URL);
    }

}
