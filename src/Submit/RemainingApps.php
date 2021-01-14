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

namespace Didapptic\Submit;

use Didapptic\Didapptic;
use Didapptic\Service\App\Filter\AppFilterService;
use Didapptic\Service\Application\I18N\TranslationService;

/**
 * Class RemainingApps
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class RemainingApps extends AbstractSubmit {

    /** @var string */
    private $chunkSize;
    /** @var TranslationService */
    private $translationService;
    /** @var AppFilterService */
    private $appFilterService;

    public function __construct(
        TranslationService $translationService
        , AppFilterService $appFilterService
    ) {
        $this->translationService = $translationService;
        $this->appFilterService   = $appFilterService;
        parent::__construct();
    }

    protected function valid(): bool {
        $this->chunkSize = (string) $this->getArgument("chunkSize");
        return true === is_numeric($this->chunkSize);
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {

        $apps    = Didapptic::getServer()->getAppsFromCache();
        $apps    = $apps->subList(
            (int) $this->chunkSize
            , $apps->length()
        );
        $baseUrl = Didapptic::getBaseURL(true);

        $this->addResponse(
            "data"
            , [
                "apps"              => $apps
                , "appleIconPath"   => $baseUrl . "/v1/resources/img/applelogo.png/image/"
                , "androidIconPath" => $baseUrl . "/v1/resources/img/androidlogo.png/image/"
                , "smartutlbPath"   => $baseUrl . "/v1/resources/img/smartutabl.png/image/"
                , "mzPath"          => $baseUrl . "/v1/resources/img/mz1.png/image/"
                , "naPath"          => $baseUrl . "/v1/resources/img/na.png/image/"
                , "forFree"         => $this->translationService->translate("gratis")
                , "number_of_apps"  => $apps->length()
            ]
        );


        return true;
    }

    protected function onDestroy(): void {

    }

}
