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

namespace Didapptic\Service\Application\I18N;

/**
 * Class TranslationService
 *
 * @package Service
 */
class TranslationService {

    /** @var string GERMAN language */
    public const GERMAN = "de_DE";
    /** @var string $language */
    private $language;

    /**
     * TranslationService constructor.
     *
     * @param string $language
     */
    public function __construct(string $language) {
        $this->setLanguage($language);
    }

    /**
     * returns the language
     *
     * @return string
     */
    public function getLanguage(): string {
        return $this->language;
    }

    /**
     * sets language
     *
     * @param string $language
     */
    public function setLanguage(string $language): void {
        $this->language = $language;
    }

    /**
     * translates a given text
     *
     * @param string      $text
     * @param string|null $default
     *
     * @return string
     */
    public function translate(string $text, string $default = null): string {

        if (null === $default) return $text;
        // TODO implement!!
        //  need to check whether there is an translation.
        //  If not, return default. If default is null,
        //  return $text
        return $text;

    }

}
