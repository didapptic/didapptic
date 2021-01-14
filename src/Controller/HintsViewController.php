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

/**
 * Class HintsViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class HintsViewController extends AbstractController {

    public function __construct() {
        parent::__construct("Hinweise");
        $this->registerJavaScript(JavaScript::HINTS_SCRIPT);
    }

    protected function onCreate(): void {

    }

    protected function create(): ?string {

        $template = parent::loadTemplate(
             View::HINTS_VIEW_HTML
        );

        $baseUrl = Didapptic::getBaseURL(true);
        return $template->render([
          // "pageInfo" => "Diese Seite befindet sich im Aufbau!",
          "useDidappticHead" => "Aufbau und Nutzung von didapptic",
          "useDidappticDescription" => "Auf der Hauptseite von didapptic werden alle bisher eingetragenen Apps in einer minimierten Ansicht aufgereiht. Du musst weiter nach unten scrollen, um weitere Apps zu laden. Um eine App im Detail zu betrachten, tippe/klicke auf den App-Titel.",
          "registrationTitle" => "Registrierung",
          "registrationDescription" => "Um Dich registrieren zu können, benötigst Du das Zugangspasswort für den Bereich zur Registrierung auf der “Login”-Seite. Mit diesem Zugangspasswort kannst Du einen eigenen Account erstellen, mit dem Du dann Apps eintragen kannst. Wir bitten Dich, das Zugangspasswort nur Kolleginnen und Kollegen weiterzugeben.",
          "appBoxTitle" => "Beschreibung der App-Box",
          "appBoxDescription" => "Um die Seite übersichtlich zu halten, das Suchen und Stöbern zu vereinfachen, werden alle Apps zunächst eingeklappt angezeigt. Wie oben beschrieben, muss jeder App-Streifen angetippt/angeklickt werden, damit weitere Informationen erscheinen.",
          "appBoxDescriptionl3h1" => "Apps in der Übersicht",
          "appBoxDescriptionl3d1" => "Wenn die App-Box zugeklappt ist, sind nur die wichtigsten Daten sichtbar: Icon (Logo der App), Name der App. Die Bewertung durch die Person, die die App eingetragen hat, erscheint als Mittelwert.",
          "appBoxDetailsHead" => "App-Details",
          "appBoxDetailsDescription" => "Wenn eine App ausgewählt wird, öffnet sich eine Detail-Ansicht, die weitere Informationen bereithält. Die Bewertung der App wird im Detail aufgeschlüsselt.",
          "appBoxDetails1" => "Didaktischer Kommentar",
          "appBoxDetailsDescrp1" => "Im didaktischen Kommentar wird die App aus (fach-)didaktischer Sicht eingeordnet und beurteilt. Wichtig ist hier, für Lesende zu klären, wie die App produktiv eingesetzt werden kann. Dabei kann es um den Einsatz im Unterricht, in der Lehre oder um informelle Bildung gehen.",
          "appBoxDetails2" => "Anmerkungen",
          "appBoxDetailsDescrp2" => "In den Anmerkungen werden eher technische odere andere, nicht-didaktische Aspekte aufgegriffen. Gibt es InApp-Käufe, Versionen für das Mobile Device Management oder andere Besonderheiten bzw. Einschränkungen bei der Nutzung der App?",
          "appboxClosed" => $baseUrl . "/v1/resources/img/appbox-closed/image/",
           "appboxOpen" => $baseUrl . "/v1/resources/img/goku/image/",
        ]);
    }

    protected function onDestroy(): void {

    }

}
