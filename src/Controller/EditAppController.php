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

use Didapptic\Object\App;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Environment;
use Didapptic\Object\Permission;
use Didapptic\Object\User;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Service\App\Metadata\MetadataService;
use Didapptic\Service\Application\I18N\TranslationService;
use Didapptic\Service\User\UserService;

/**
 * Class EditAppController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class EditAppController extends AbstractController {

    /** @var Environment $properties */
    private $properties;
    /** @var App|null $app */
    private $app            = null;
    private $privacy        = null;
    private $subjects       = null;
    private $tags           = null;
    private $categories     = null;
    private $recommendation = null;
    /** @var TranslationService */
    private $translationService;
    /** @var UserService */
    private $userService;
    /** @var AppRepository */
    private $appManager;
    /** @var MetadataService */
    private $metadataService;

    public function __construct(
        Environment $properties
        , TranslationService $translationService
        , UserService $userService
        , AppRepository $appManager
        , MetadataService $metadataService
    ) {

        parent::__construct($translationService->translate("App bearbeiten"));

        parent::registerJavaScript(JavaScript::EDIT_APP_SCRIPT);

        $this->properties         = $properties;
        $this->translationService = $translationService;
        $this->userService        = $userService;
        $this->appManager         = $appManager;
        $this->metadataService    = $metadataService;
    }

    protected function onCreate(): void {
        $storeId              = $this->getArgument("storeId");
        $this->recommendation = $this->metadataService->getMetadata("recommendation");
        $this->categories     = $this->metadataService->getMetadata("category");
        $this->tags           = $this->metadataService->getMetadata("tag");
        $this->subjects       = $this->metadataService->getMetadata("subject");
        $this->privacy        = $this->metadataService->getMetadata("privacy");
        $this->app            = $this->appManager->getAppByStoreId($storeId);
    }

    /**
     * @return string|null
     */
    protected function create(): ?string {

        if (null === $this->app) {

            $template = $this->loadTemplate(
                $this->getTemplatePath()
                , View::ALERT_VIEW
            );

            return $template->render(
                [
                    "text" => $this->translationService->translate("App wurde nicht gefunden!")
                ]
            );

        }

        $template = $this->loadTemplate(
            $this->getTemplatePath()
            , View::APP_FORM_VIEW
        );

        /** @var User $user */
        $user                 = $this->userService->getUser();
        $appDeletionPermitted = $this->hasPermission(Permission::APP_DELETION);
        $newSubjectPermitted  = $this->hasPermission(Permission::SUBMIT_NEW_SUBJECT);
        $newCategoryPermitted = $this->hasPermission(Permission::SUBMIT_NEW_CATEGORY);
        $newTagPermitted      = $this->hasPermission(Permission::SUBMIT_NEW_TAG);
        $debug                = $this->properties->isDebug();

        return $template->render(
            [
                // label
                "headline"                     => $this->translationService->translate("App bearbeiten")
                , "editAppDescription"         => $this->translationService->translate("Bereits eingetragene Apps können Sie hier bearbeiten. Sie haben die Möglichkeit alle Angaben bis auf den App-Link zu verändern.")
                , "androidDurationHint"        => $this->translationService->translate("Hinweis: aus technischen Gründen dauert das Speichern von Apps aus dem Google Play Store länger.")
                , "googleStoreUrlLabel"        => $this->translationService->translate("Google Store URL")
                , "googleStoreUrlPlaceHolder"  => $this->translationService->translate("Google Store URL")
                , "iosStoreUrlLabel"           => $this->translationService->translate("iOS Store URL")
                , "iosStoreUrlPlaceHolder"     => $this->translationService->translate("iOS Store URL")
                , "usageLabel"                 => $this->translationService->translate("Nutzungskomfort")
                , "usagePlaceholder"           => $this->translationService->translate("Zu bewerten sind Menüführung, Übersichtlichkeit, Leichtigkeit der Bedienung.")
                , "resultsQuality"             => $this->translationService->translate("Ergebnis-Qualität")
                , "resultsQualityPlaceholder"  => $this->translationService->translate("Zu bewerten sind Qualität des Produktes (Auflösung, Datei-Art > Formatvielfalt), sinnvolle & hilfreiche Bearbeitungsmöglichkeiten (Bsp. Stop-Motion: voriges Bild wird angezeigt, Vorder/Hintergrund können bearbeitet werden) für die jeweilige Anwendung.")
                , "presentabilityLabel"        => $this->translationService->translate("Präsentierbarkeit der Ergebnisse")
                , "presentabilityPlaceholder"  => $this->translationService->translate("Zu bewerten sind Vielfalt bei Export und Teilen der Datei, ggf. eigene Präsentationsform (Vorschau des Erstellten). Sinnvoll zu bewerten unter dem Aspekt des Notwendigen für die jeweilige Anwendung.")
                , "didacticCommentLabel"       => $this->translationService->translate("Didaktischer Kommentar")
                , "didacticCommentPlaceholder" => $this->translationService->translate("Didaktischer Kommentar")
                , "didacticRemarkLabel"        => $this->translationService->translate("Anmerkungen")
                , "didacticRemarkPlaceholder"  => $this->translationService->translate("Anmerkungen")
                , "privacyLabel"               => $this->translationService->translate("Datenschutz")
                , "privacyDescription"         => $this->translationService->translate("Wählen Sie bitte zwischen den Optionen.")
                , "privacyCommentLabel"        => $this->translationService->translate("Optional: Kommentar zum Datenschutz")
                , "privacyCommentPlaceholder"  => $this->translationService->translate("Kommentar zum Datenschutz, bspw. ob und welche personenbezogenenen Daten erhoben werden. Ob dies berechtigt ist, etc.")
                , "subjectsLabel"              => $this->translationService->translate("Fächer")
                , "subjectsDescription"        => $this->translationService->translate("Wählen Sie alle Fächer (Mehrfachnennungen sind möglich), für die Sie sich eine Nutzung der App vorstellen können. Inhaltlich können Sie beim didaktischen Kommentar hierauf eingehen. Da für ähnliche Fächer unterschiedliche Bezeichnungen existieren, wurde eine Berücksichtigung versucht. Wenn Sie Ihr Fach nicht finden, tippen Sie auf ähnliche Fächer oder kontaktieren Sie uns, damit wir es in unsere Liste aufnehmen können. Insbesondere Fächer an berufsbildenden Schulen können hier nur bedingt abgebildet werden, hier freuen wir uns auf Ihre Anregungen.")
                , "categoryLabel"              => $this->translationService->translate("Kategorien")
                , "categoryDescription"        => $this->translationService->translate("Wählen Sie alle Kategorien, die zu dieser App passen und/oder tragen Sie passende Kategorien neu ein.")
                , "tagsLabel"                  => $this->translationService->translate("Stichworte")
                , "categoryDesription"         => $this->translationService->translate("Wählen Sie alle Kategorien, die zu dieser App passen und/oder tragen Sie passende Kategorien neu ein.")
                , "authorLabel"                => $this->translationService->translate("Autor des Eintrages")
                , "authorDescription"          => $this->translationService->translate("Wählen Sie bitte den Autor des Eintrages.")
                , "recommendationLabel"        => $this->translationService->translate("Abschluss")
                , "recommendationDescription"  => $this->translationService->translate("Die App ist insgesamt")
                , "submitButtomLabel"          => $this->translationService->translate("Daten aktualisieren")
                , "iosPrivacy"                 => $this->translationService->translate("Datenschutzrichtlinie für iOS-App:  (iOS only)")
                , "iosPrivacyPlaceholder"      => $this->translationService->translate("Datenschutzrichtlinie für iOS-App:  (iOS only)")
                , "deleteAppButtonText"        => $this->translationService->translate("Löschen")
                , "confirmDeletion"            => $this->translationService->translate("Löschen bestätigen")
                , "confirmDeletionQuestion"    => $this->translationService->translate("Sie sind dabei die App zu löschen. Möchten Sie dies wirklich tun?")
                , "cancel"                     => $this->translationService->translate("Abbrechen")
                , "delete"                     => $this->translationService->translate("Löschen")
                , "usageDescription"           => $this->translationService->translate("Zu bewerten sind Aspekte wie Menüführung, Übersichtlichkeit, Leichtigkeit der Bedienung. Wie komfortabel lässt sich die Applikation nutzen?")
                , "resultsQualityDescription"  => $this->translationService->translate("Zu bewerten sind Qualität des Produktes (Auflösung, Datei-Art > Formatvielfalt), sinnvolle & hilfreiche Bearbeitungsmöglichkeiten (Bsp. Stop-Motion: voriges Bild wird angezeigt (Zwiebelhauteffekt), Vorder-/Hintergrund können bearbeitet werden) für die jeweilige Anwendung. Wie gut eignet sich die Applikation für den vorgesehenen Anwendungszweck? Die Ergebnis-Qualität unterscheidet Apps gleicher Klasse nach Güte: “Eine gute Stop-Motion-App bietet zumindest die Möglichkeit zur Änderung der Framerate an.”")
                , "presentabilityDescription"  => $this->translationService->translate("Zu bewerten sind Vielfalt bei Export und Teilen der Datei, ggf. eigene Präsentationsform (Vorschau des Erstellten). Sinnvoll zu bewerten unter dem Aspekt des Notwendigen für die jeweilige Anwendung.")
                , "didacticCommentDescription" => $this->translationService->translate("Der didaktische Kommentar dient Lesenden dazu, die App-Nutzung im Unterricht (Lehre) einzuordnen. Wofür soll oder kann die App eingesetzt werden? Worauf muss in der didaktischen Planung eventuell geachtet werden? Welche produktiven, kreativen Prozesse, Methoden und andere Medien sind ggf. involviert?")
                , "didacticRemarkDescription"  => $this->translationService->translate("Unter Anmerkungen fallen alle weiteren Anmerkungen, die nicht primär didaktisch orientiert sind. Gibt es technische Besonderheiten, die zu beachten wären? Ist bspw. die Auflösung der Fotos beschränkt? Handelt es sich um eine eingeschränkte, kostenlose Version? Wird (viel) Werbung eingeblendet?")
                , "tagsDesription"             => $this->translationService->translate("Wählen Sie alle Stichworte, die zu dieser App passen. Kontaktieren Sie uns gerne, falls Stichworte fehlen. Sie werden bald eigene Stichworte ergänzen können")
                , "newCategory"                => $this->translationService->translate("neue Kategorie")
                , "newTag"                     => $this->translationService->translate("neues Tag")
                , "newSubject"                 => $this->translationService->translate("neues Fach")
                , "urlsLabel"                  => $this->translationService->translate("App Store URL's")
                , "userInput"                  => $this->translationService->translate("Benutzerangaben")

                // data
                , "tags"                       => $this->tags
                , "recommendations"            => $this->recommendation
                , "categories"                 => $this->categories
                , "privacy"                    => $this->privacy
                , "subjects"                   => $this->subjects
                , "debug"                      => $debug
                , "authors"                    => [$user]
                , "displayIosPrivacy"          => $this->app->isAndroid() ? "display: none" : ""
                , "app"                        => $this->app
                , "readonly"                   => true

                // permission
                , "permitted"                  => $appDeletionPermitted
                , "appDeletionPermitted"       => $appDeletionPermitted
                , "newSubjectPermitted"        => $newSubjectPermitted
                , "newCategoryPermitted"       => $newCategoryPermitted
                , "newTagPermitted"            => $newTagPermitted
            ]
        );
    }

    protected function onDestroy(): void {

    }

}
