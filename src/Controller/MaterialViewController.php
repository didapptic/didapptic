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

use DateTime;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Permission;
use Didapptic\Repository\MaterialRepository;
use Didapptic\Service\File\Mime\MimeService;

/**
 * Class MaterialViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MaterialViewController extends AbstractController {

    /** @var MaterialRepository */
    private $materialManager;
    /** @var MimeService */
    private $mimeService;

    public function __construct(
        MaterialRepository $materialManager
        , MimeService $mimeService
    ) {
        parent::__construct("Material");
        $this->registerJavaScript(JavaScript::MATERIAL_SCRIPT);

        $this->materialManager = $materialManager;
        $this->mimeService     = $mimeService;
    }

    protected function onCreate(): void {

    }

    protected function create(): ?string {

        $template = parent::loadTemplate(
            View::MATERIAL_VIEW
        );

        parent::registerCss(
            "https://use.fontawesome.com/releases/v5.5.0/css/all.css"
        );

        $newMaterial    = parent::hasPermission(Permission::MENU_NEW_MATERIAL);
        $deleteMaterial = parent::hasPermission(Permission::MENU_DELETE_MATERIAL);

        $material       = $this->materialManager->getAll();
        $supportedMimes = $this->mimeService->getSupportedMimeTypes();

        $supportedMimes = implode(", ", array_keys($supportedMimes));
        $supportedMimes = strtoupper($supportedMimes);

        return $template->render([
            // label
            "headerOne"                                 => "Material zu Fortbildungen"
            , "newMaterialHeader"                       => "neues Material hochladen"
            , "newMaterialDescription"                  => "Hier können Sie neues Material hochladen"
            , "dateInputLabelDescription"               => "Datum"
            , "dateInputDescriptionPlaceholder"         => "Datum"
            , "materialDescription"                     => "Beschreibung"
            , "materialDescriptionPlaceholder"          => "Beschreibung"
            , "materialUploadFileLabel"                 => "Dateien"
            , "submitButtomLabel"                       => "Speichern"
            , "today"                                   => (new DateTime())->format("d.m.Y")
            , "dateInputNameLabelDescription"           => "Name"
            , "dateInputNameDescriptionPlaceholder"     => "Name"
            , "dateInputPasswordLabelDescription"       => "Passwort"
            , "dateInputPasswordDescriptionPlaceholder" => "Passwort"
            , "inputPasswordPlaceholder"                => "Passwort"
            , "supportedMimesDesc"                      => "Die nachfolgenden Dateiendungen werden unterstützt: $supportedMimes"

            // data
            , "materials"                               => $material

            // permission
            , "canAddNewMaterial"                       => $newMaterial
            , "canDeleteMaterial"                       => $deleteMaterial
        ]);
    }

    protected function onDestroy(): void {

    }

}
