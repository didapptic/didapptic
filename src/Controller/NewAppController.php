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

use Didapptic\Backend\Processor;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Environment;
use Didapptic\Object\Permission;
use Didapptic\Service\App\Metadata\MetadataService;
use Didapptic\Service\Application\I18N\TranslationService;
use Didapptic\Service\User\UserService;

/**
 * Class NewAppController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class NewAppController extends AbstractController {

    private $categories      = null;
    private $privacy         = null;
    private $recommendations = null;
    private $studySubject    = null;
    private $tags            = null;
    private $properties      = null;
    /** @var UserService */
    private $userService;
    /** @var Processor */
    private $processor;
    /** @var TranslationService */
    private $translationService;
    /** @var MetadataService */
    private $metadataService;

    public function __construct(
        Environment $properties
        , UserService $userService
        , Processor $processor
        , TranslationService $translationService
        , MetadataService $metadataService
    ) {

        parent::__construct("Neuer Eintrag");

        parent::registerJavaScript(JavaScript::NEW_APP_SCRIPT);

        $this->properties         = $properties;
        $this->userService        = $userService;
        $this->processor          = $processor;
        $this->translationService = $translationService;
        $this->metadataService    = $metadataService;
    }

    protected function onCreate(): void {
        $this->categories      = $this->metadataService->getMetadata("category");
        $this->privacy         = $this->metadataService->getMetadata("privacy");
        $this->recommendations = $this->metadataService->getMetadata("recommendation");
        $this->studySubject    = $this->metadataService->getMetadata("subject");
        $this->tags            = $this->metadataService->getMetadata("tag");
    }

    /**
     * @return string|null
     */
    protected function create(): ?string {

        $user = $this->userService->getUser();

        $template = $this->loadTemplate(
            $this->getTemplatePath()
            , View::APP_FORM_VIEW
        );

        $newSubjectPermitted  = $this->hasPermission(Permission::SUBMIT_NEW_SUBJECT);
        $newCategoryPermitted = $this->hasPermission(Permission::SUBMIT_NEW_CATEGORY);
        $newTagPermitted      = $this->hasPermission(Permission::SUBMIT_NEW_TAG);
        $debug                = $this->properties->isDebug();

        return $template->render([
                // label
                "headline"                       => $this->translationService->translate("Neue App")
                , "editAppDescription"           => $this->translationService->translate("Ihre Einträge sollen Kolleginnen und Kollegen sowie anderen Interessierten als Orientierung dafür dienen, ob sie bestimmte Apps in Ihrer Lehre einsetzen sollten oder nicht. Das Projekt baut auf Kooperation auf. Die Grundlage des Austausches sollten ehrliche und möglichst präzise Bewertungen sein, die auf pädagogischen und didaktischen Kriterien zur Auswahl von Apps basieren. Als Plattform-Betreiber verzichten wir daher bewusst auf Provisionen und Affiliate-Programme, den gleichen Anspruch stellen wir an alle Nutzerinnen und Nutzer. Wir bedanken uns für Ihr Interesse und Ihre Unterstützung!")
                , "androidDurationHint"          => $this->translationService->translate("Hinweis: aus technischen Gründen dauert das Speichern von Apps aus dem Google Play Store länger.")
                , "googleStoreUrlLabel"          => $this->translationService->translate("Google Store URL")
                , "googleStoreUrlPlaceHolder"    => $this->translationService->translate("Google Store URL")
                , "iosStoreUrlLabel"             => $this->translationService->translate("iOS Store URL")
                , "iosStoreUrlPlaceHolder"       => $this->translationService->translate("iOS Store URL")
                , "usageLabel"                   => $this->translationService->translate("Nutzungskomfort")
                , "usageDescription"             => $this->translationService->translate("Zu bewerten sind Aspekte wie Menüführung, Übersichtlichkeit, Leichtigkeit der Bedienung. Wie komfortabel lässt sich die Applikation nutzen?")
                , "resultsQuality"               => $this->translationService->translate("Ergebnis-Qualität")
                , "resultsQualityDescription"    => $this->translationService->translate("Zu bewerten sind Qualität des Produktes (Auflösung, Datei-Art > Formatvielfalt), sinnvolle & hilfreiche Bearbeitungsmöglichkeiten (Bsp. Stop-Motion: voriges Bild wird angezeigt (Zwiebelhauteffekt), Vorder-/Hintergrund können bearbeitet werden) für die jeweilige Anwendung. Wie gut eignet sich die Applikation für den vorgesehenen Anwendungszweck? Die Ergebnis-Qualität unterscheidet Apps gleicher Klasse nach Güte: “Eine gute Stop-Motion-App bietet zumindest die Möglichkeit zur Änderung der Framerate an.”")
                , "presentabilityLabel"          => $this->translationService->translate("Präsentierbarkeit der Ergebnisse")
                , "presentabilityDescription"    => $this->translationService->translate("Zu bewerten sind Vielfalt bei Export und Teilen der Datei, ggf. eigene Präsentationsform (Vorschau des Erstellten). Sinnvoll zu bewerten unter dem Aspekt des Notwendigen für die jeweilige Anwendung.")
                , "didacticCommentLabel"         => $this->translationService->translate("Didaktischer Kommentar")
                , "didacticCommentPlaceholder"   => $this->translationService->translate("Didaktischer Kommentar")
                , "didacticCommentDescription"   => $this->translationService->translate("Der didaktische Kommentar dient Lesenden dazu, die App-Nutzung im Unterricht (Lehre) einzuordnen. Wofür soll oder kann die App eingesetzt werden? Worauf muss in der didaktischen Planung eventuell geachtet werden? Welche produktiven, kreativen Prozesse, Methoden und andere Medien sind ggf. involviert?")
                , "didacticRemarkLabel"          => $this->translationService->translate("Anmerkungen")
                , "didacticRemarkPlaceholder"    => $this->translationService->translate("Anmerkungen")
                , "didacticRemarkDescription"    => $this->translationService->translate("Unter Anmerkungen fallen alle weiteren Anmerkungen, die nicht primär didaktisch orientiert sind. Gibt es technische Besonderheiten, die zu beachten wären? Ist bspw. die Auflösung der Fotos beschränkt? Handelt es sich um eine eingeschränkte, kostenlose Version? Wird (viel) Werbung eingeblendet?")
                , "privacyLabel"                 => $this->translationService->translate("Datenschutz")
                , "privacyDescription"           => $this->translationService->translate("Wählen Sie bitte zwischen den Optionen.")
                , "privacyCommentLabel"          => $this->translationService->translate("Optional: Kommentar zum Datenschutz")
                , "privacyCommentPlaceholder"    => $this->translationService->translate("Kommentar zum Datenschutz, bspw. ob und welche personenbezogenenen Daten erhoben werden. Ob dies berechtigt ist, etc.")
                , "subjectsLabel"                => $this->translationService->translate("Fächer")
                , "subjectsDescription"          => $this->translationService->translate("Wählen Sie alle Fächer (Mehrfachnennungen sind möglich), für die Sie sich eine Nutzung der App vorstellen können. Inhaltlich können Sie beim didaktischen Kommentar hierauf eingehen. Da für ähnliche Fächer unterschiedliche Bezeichnungen existieren, wurde eine Berücksichtigung versucht. Wenn Sie Ihr Fach nicht finden, tippen Sie auf ähnliche Fächer oder kontaktieren Sie uns, damit wir es in unsere Liste aufnehmen können. Insbesondere Fächer an berufsbildenden Schulen können hier nur bedingt abgebildet werden, hier freuen wir uns auf Ihre Anregungen.")
                , "categoryLabel"                => $this->translationService->translate("Kategorien")
                , "categoryDescription"          => $this->translationService->translate("Wählen Sie alle Kategorien, die zu dieser App passen und/oder tragen Sie passende Kategorien neu ein.")
                , "tagsLabel"                    => $this->translationService->translate("Stichworte")
                , "tagsDesription"               => $this->translationService->translate("Wählen Sie alle Stichworte, die zu dieser App passen. Kontaktieren Sie uns gerne, falls Stichworte fehlen. Sie werden bald eigene Stichworte ergänzen können")
                , "authorLabel"                  => $this->translationService->translate("Autor des Eintrages")
                , "authorDescription"            => $this->translationService->translate("Der Autor des Eintrages sind Sie.")
                , "recommendationLabel"          => $this->translationService->translate("Abschluss")
                , "recommendationDescription"    => $this->translationService->translate("Entscheiden Sie bitte abschließend. Sie können freilich auch Apps eintragen, die Sie nach Ihrer Erfahrung für den Einsatz im Unterricht nicht empfehlen können. Die App ist insgesamt ...")
                , "submitButtomLabel"            => $this->translationService->translate("App speichern")
                , "iosPrivacy"                   => $this->translationService->translate("Datenschutzrichtlinie für iOS-App:  (iOS only)")
                , "iosPrivacyPlaceholder"        => $this->translationService->translate("Datenschutzrichtlinie für iOS-App:  (iOS only)")
                , "subjectsUniversalDescription" => $this->translationService->translate("Bitte entscheiden Sie sich, ob die bewertete App nur in bestimmten Fächern oder universell einsetzbar ist. Es ist nicht möglich, beides zu wählen. Wenn Sie 'universell' wählen, bedeutet dies, dass die App in jedem Fach oder unabhängig von einem Fach eingesetzt werden kann.")
                , "usagePlaceholder"             => $this->translationService->translate("Zu bewerten sind Menüführung, Übersichtlichkeit, Leichtigkeit der Bedienung.")
                , "resultsQualityPlaceholder"    => $this->translationService->translate("Zu bewerten sind Qualität des Produktes (Auflösung, Datei-Art > Formatvielfalt), sinnvolle & hilfreiche Bearbeitungsmöglichkeiten (Bsp. Stop-Motion: voriges Bild wird angezeigt, Vorder/Hintergrund können bearbeitet werden) für die jeweilige Anwendung.")
                , "presentabilityPlaceholder"    => $this->translationService->translate("Zu bewerten sind Vielfalt bei Export und Teilen der Datei, ggf. eigene Präsentationsform (Vorschau des Erstellten). Sinnvoll zu bewerten unter dem Aspekt des Notwendigen für die jeweilige Anwendung.")
                , "categoryDesription"           => $this->translationService->translate("Wählen Sie alle Kategorien, die zu dieser App passen und/oder tragen Sie passende Kategorien neu ein.")
                , "deleteAppButtonText"          => $this->translationService->translate("Löschen")
                , "confirmDeletion"              => $this->translationService->translate("Löschen bestätigen")
                , "confirmDeletionQuestion"      => $this->translationService->translate("Sie sind dabei die App zu löschen. Möchten Sie dies wirklich tun?")
                , "cancel"                       => $this->translationService->translate("Abbrechen")
                , "delete"                       => $this->translationService->translate("Löschen")
                , "newCategory"                  => $this->translationService->translate("neue Kategorie")
                , "newTag"                       => $this->translationService->translate("neues Tag")
                , "newSubject"                   => $this->translationService->translate("neues Fach")
                , "urlsLabel"                    => $this->translationService->translate("App Store URL's")
                , "userInput"                    => $this->translationService->translate("Benutzerangaben")

                // data
                , "tags"                         => $this->tags
                , "privacy"                      => $this->privacy
                , "subjects"                     => $this->studySubject
                , "categories"                   => $this->categories
                , "authors"                      => [$user]
                , "debug"                        => $debug
                , "recommendations"              => $this->recommendations
                , "readOnly"                     => false

                // permission
                , "permitted"                    => $user !== null
                , "displayIosPrivacy"            => true
                , "appDeletionPermitted"         => false
                , "newSubjectPermitted"          => $newSubjectPermitted
                , "newCategoryPermitted"         => $newCategoryPermitted
                , "newTagPermitted"              => $newTagPermitted
                , "app"                          => null
            ]
        );
    }

    protected function onDestroy(): void {

    }

}
