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
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Permission;
use Didapptic\Service\App\Metadata\MetadataService;

class NewUserViewController extends AbstractController {

    private $subjects = null;
    /** @var MetadataService */
    private $metadataService;

    public function __construct(MetadataService $metadataService) {
        parent::__construct("Registrieren");
        parent::registerJavaScript(JavaScript::NEW_USER);
        $this->metadataService = $metadataService;
    }

    protected function onCreate(): void {
        $this->subjects = $this->metadataService->getMetadata("subject");
    }

    /**
     * @return string|null
     */
    protected function create(): ?string {

        $hasPermission = parent::hasPermission(
            Permission::SUBMIT_NEW_USER
        );

        $template = parent::loadTemplate(
            parent::getTemplatePath()
            , View::NEW_USER_VIEW
        );

        if (isset($this->subjects[-1])) unset($this->subjects[-1]);
        if (isset($this->subjects[26])) unset($this->subjects[26]);

        $baseUrl = Didapptic::getBaseURL(true);
        return $template->render([
            "pageTitle"                   => "Registrieren"
            , "firstNameLabel"            => "Vorname"
            , "subjectsLabel"             => "Fächer"
            , "subjects"                  => $this->subjects
            , "firstNamePlaceHolder"      => "Vorname"
            , "lastNameLabel"             => "Nachname"
            , "lastNamePlaceHolder"       => "Nachname"
            , "userNameLabel"             => "Benutzername"
            , "userNamePlaceHolder"       => "Benutzername"
            , "passwordLabel"             => "Passwort"
            , "passwordPlaceHolder"       => "Passwort"
            , "passwordRepeatLabel"       => "Passwort wiederholen"
            , "passwordRepeatPlaceHolder" => "Passwort wiederholen"
            , "emailLabel"                => "E-Mail Adresse"
            , "emailPlaceHolder"          => "E-Mail Adresse"
            , "subjectsDescription"       => "Wählen Sie hier bitte die Fächer aus, für die Sie eine Lehrbefähigung haben. Falls ihr Fach nicht dabei ist, kontaktieren Sie uns gerne, wir fügen es gerne hinzu."
            , "websiteUrlLabel"           => "Ihre Webseite"
            , "websiteUrlPlaceHolder"     => "Ihre Webseite"
            , "newsletterLabel"           => "Newsletter"
            , "newsletterPlaceHolder"     => "Ich möchte zukünftig per Mail über Neuigkeiten zu didapptic informiert werden"
            , "submitButtomLabel"         => "Registrieren"
            , "passwordPolicy"            => "Das Passwort muss mindestens folgende Anforderungen erfüllen:<br><ul><li>mindestens 8 Zeichen</li><li>mindestens einen Großbuchstaben</li><li>mindestens ein Kleinbuchstaben</li><li>mindestens eine Zahl</li></ul>"
            , "optionalHint"              => "mit * gekennzeichnete Stellen sind Pflichtfelder"
            , "privacyPolicyHint"         => "mit dem Klick auf den Button erklären Sie sich mit unserer <a href='$baseUrl/menu/privacy/'>Datenschutzvereinbarung</a> einverstanden"
            , "permitted"                 => $hasPermission,
        ]);
    }

    protected function onDestroy(): void {

    }

}
