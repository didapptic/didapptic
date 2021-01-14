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

namespace Didapptic\Controller;

use Didapptic\Didapptic;
use Didapptic\Object\Constant\View;

/**
 * Class PrivacyViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class PrivacyViewController extends AbstractController {

    public function __construct() {
        parent::__construct("Datenschutz");
    }

    protected function onCreate(): void {

    }

    protected function create(): ?string {

        $template = parent::loadTemplate(
            View::PRIVACY_VIEW
        );

        $baseUrl = Didapptic::getBaseURL(true);
        return $template->render([
            "firstName"         => "Ahmet"
            , "lastName"        => "Camuka"
            , "addressLabel"    => "Dienstliche Anschrift:"
            , "street"          => "Muckermannstr."
            , "number"          => "1"
            , "zip"             => "60488"
            , "city"            => "Frankfurt am Main"
            , "country"         => "Deutschland (Germany)"
            , "mailLabel"       => "E-Mail Adresse"
            , "mail"            => "info[at]didapptic.com"
            , "linkToImpressum" => "Link zum Impressum"
            , "impressumLink"   => "<a href='{$baseUrl}/menu/imprint/'>{$baseUrl}/menu/imprint/</a>"
        ]);
    }

    protected function onDestroy(): void {

    }

}
