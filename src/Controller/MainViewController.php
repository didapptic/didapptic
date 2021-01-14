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
use Didapptic\Object\App;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Environment;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Service\App\Metadata\MetadataService;
use Didapptic\Service\Application\I18N\TranslationService;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class MainViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MainViewController extends AbstractController {

    /** @var ArrayList */
    private $apps;
    /** @var null|array */
    private $categories;
    /** @var null|array */
    private $subjects;
    /** @var AppRepository */
    private $appManager;
    /** @var Environment */
    private $environment;
    /** @var MetadataService */
    private $metadataService;
    /** @var TranslationService */
    private $translationService;
    /** @var int */
    private $position = 0;

    public function __construct(
        AppRepository $appManager
        , Environment $environment
        , MetadataService $metadataService
        , TranslationService $translationService
    ) {
        parent::__construct("");
        parent::registerJavaScript(JavaScript::MAIN_SCRIPT);
        $this->appManager         = $appManager;
        $this->environment        = $environment;
        $this->metadataService    = $metadataService;
        $this->translationService = $translationService;
    }

    protected function onCreate(): void {
        $allApps          = Didapptic::getServer()->getAppsFromCache();
        $this->apps       = $allApps->subList(
            $this->position
            , $this->environment->getChunkSize()
        );
        $this->categories = $this->metadataService->getMetadata("category");
        $this->subjects   = $this->metadataService->getMetadata("subject");
    }

    /**
     * @return string|null
     */
    protected function create(): ?string {

        $template = $this->loadTemplate(
             View::MAIN_VIEW
        );

        $baseUrl = Didapptic::getBaseURL(true);

        return $template->render([
                // values
                "applications"              => $this->apps
                , "categories"              => $this->categories
                , "subjects"                => $this->subjects
                , "position"                => $this->position
                , "chunkSize"               => $this->environment->getChunkSize()

                // links
                , "appleIconPath"           => $baseUrl . "/v1/resources/img/applelogo.png/image/"
                , "androidIconPath"         => $baseUrl . "/v1/resources/img/androidlogo.png/image/"
                , "smartutlbPath"           => $baseUrl . "/v1/resources/img/smartutabl.png/image/"
                , "mzPath"                  => $baseUrl . "/v1/resources/img/mz1.png/image/"
                , "naPath"                  => $baseUrl . "/v1/resources/img/na.png/image/"
                , "hintLink"                => $baseUrl . "/menu/hints/"

                // strings
                , "filterCommentPre"        => $this->translationService->translate("Nutze den App-Filter, um die gewünschte App zu finden. Die Anzeige aktualisiert sich automatisch. Tippe auf einen Streifen, um mehr Infos über eine App anzuzeigen. ")
                , "here"                    => $this->translationService->translate("Hier")
                , "filterCommentPostFirst"  => $this->translationService->translate(" erfährst du mehr zur Verwendung von didapptic.")
                , "filterCommentPostSecond" => $this->translationService->translate("Zur Suche einzelner App-Titel kann das Feld \"App-Titel filtern\" genutzt werden.")
                , "loadAppsButton"          => $this->translationService->translate("weitere Apps laden")
                , "forFree"                 => $this->translationService->translate("gratis")
                , "searchPlaceholder"       => $this->translationService->translate("App-Titel filtern")
                , "searchLabel"             => $this->translationService->translate("App-Titel filtern")
                , "osLabel"                 => $this->translationService->translate("Betriebssystem")
                , "subjectLabel"            => $this->translationService->translate("Fach")
                , "categoryLabel"           => $this->translationService->translate("Kategorie")
                , "showInfo"                => $this->translationService->translate("Info einblenden")
                , "androidHeader"           => $this->translationService->translate("Android Apps")
                , "operatingSystems"        =>
                    [
                        App::IOS       => App::IOS_NAME
                        , App::ANDROID => App::ANDROID_NAME
                    ]

            ]
        );
    }

    protected function onDestroy(): void {

    }

}
