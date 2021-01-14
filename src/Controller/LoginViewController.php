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
use Didapptic\Object\Constant\CSS;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;

/**
 * Class LoginViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class LoginViewController extends AbstractController {

    public function __construct() {
        parent::__construct("Login");
        parent::registerJavaScript(JavaScript::LOGIN_VIEW);
        parent::registerCss(CSS::LOGIN_CSS);
    }

    protected function onCreate(): void {

    }

    protected function create(): ?string {

        $template = parent::loadTemplate(
             View::LOGIN_VIEW
        );

        $baseUrl          = Didapptic::getBaseURL(true);
        $registrationLink = "$baseUrl/menu/new-user/";
        $passwordLostLink = "$baseUrl/menu/password-lost/";
        $expired          = $this->getArgument("expired") ?? "false";

        return $template->render([
            "signintitle"           => "Einloggen"
            , "signInDescription"   => "Loggen Sie sich mit Ihrem Benutzernamen und Passwort ein."
            , "usernameLabel"       => "Benutzername"
            , "usernamePlaceholder" => "Benutzername"
            , "passwordtitle"       => "Passwort"
            , "passwordPlaceholder" => "Passwort"
            , "loginButtonText"     => "Einloggen"
            , "expiredText"         => "Ihre Session ist leider abgelaufen. Bitte melden Sie sich erneut an"
            , "expired"             => "true" === $expired
            , "credentialsText"     => 'Falls Sie noch keine Zugangsdaten haben, kontaktieren Sie uns. Sie erhalten dann einen Zugang zur Registrierung. Sobald Sie diese Daten haben, klicken Sie auf <a href="' . $registrationLink . '">Registrierung</a>, um einen eigenen Account zu erhalten.'
            , "passwordLostText"    => "Falls Sie Ihr Passwort vergessen haben klicken Sie bitte <a href='$passwordLostLink'>hier.</a>"
        ]);

    }

    protected function onDestroy(): void {

    }

}
