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

use DateTimeInterface;
use Didapptic\Didapptic;
use Didapptic\Object\App;
use Didapptic\Object\Environment;
use Didapptic\Object\Permission;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Repository\PrivacyRepository;
use function html_entity_decode;
use function nl2br;

/**
 * Class AppDetailViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AppDetailViewController extends AbstractController {

    /** @var App */
    private $app;
    /** @var AppRepository */
    private $appManager;
    /** @var Environment */
    private $environment;
    /** @var PrivacyRepository */
    private $privacyRepository;

    public function __construct(
        Environment $environment
        , AppRepository $appManager
        , PrivacyRepository $privacyRepository
    ) {
        parent::__construct("");
        $this->environment       = $environment;
        $this->appManager        = $appManager;
        $this->privacyRepository = $privacyRepository;
    }

    protected function onCreate(): void {
        $appId = $this->getArgument("appId");
        /** @var @phpstan-ignore-next-line */
        $this->app = $this->appManager->getAppByStoreId($appId);
    }

    /**
     * @return string|null
     */
    protected function create(): ?string {
        $appEditPermitted = $this->hasPermission(Permission::APP_EDIT);

        $lastUserUpdatTs    = $this->getDateDescription($this->app->getLastUserUpdateTs());
        $lastUpdated        = $this->getDateDescription($this->app->getLastUpdated());
        $insertDate         = $this->getDateDescription($this->app->getCreateTs());
        $timeArray          = $this->getTimestampDescriptions();
        $appStoreIconUrl    = $this->getAppStoreIcon($this->app->getOperatingSystem());
        $privacyDescription = 'Keine Angaben';

        $minimumOsLabel = "Erfordert mindestens iOS-Version:";
        if ($this->app->getOperatingSystem() === App::ANDROID) {
            $minimumOsLabel = "Erfordert mindestens Android-Version:";
        }

        $privacyArray = $this->privacyRepository->getPrivacy();

        foreach ($privacyArray as $id => $privacyData) {
            if ((int) $id === (int) $this->app->getPrivacy()) {
                $privacyDescription = $privacyData;
            }
        }

        $didacticComment = nl2br($this->app->getDidacticComment());
        $description     = nl2br($this->app->getDescription());
        $remark          = nl2br($this->app->getDidacticRemark());
        $privacy         = nl2br($privacyDescription);
        $privacyComment  = nl2br($this->app->getPrivacyComment());

        $didacticComment = html_entity_decode($didacticComment);
        $description     = html_entity_decode($description);
        $remark          = html_entity_decode($remark);
        $privacy         = html_entity_decode($privacy);
        $privacyComment  = html_entity_decode($privacyComment);

        return (string) json_encode([
            // values
            "app_store_icon_url"    => $appStoreIconUrl
            , "insertDate"          => $insertDate
            , "updateDate"          => $lastUserUpdatTs
            , "didacticComment"     => $didacticComment
            , "remarks"             => $remark
            , "privacyComment"      => $privacyComment
            , "privacyInformation"  => $privacy
            , "lastUpdated"         => $lastUpdated
            , "minimumOsLabel"      => $minimumOsLabel
            , "appStoreDescription" => $description
            , "appEditPermitted"    => $appEditPermitted
            , "versionUpdate"       => $lastUpdated
            , "timeArray"           => $timeArray

            // app data
            , "store_id"            => $this->app->getStoreId()
            , "developerWebsite"    => $this->app->getDeveloperWebsite()
            , "app_id"              => $this->app->getId()
            , "applicationName"     => $this->app->getName()
            , "usageValue"          => $this->app->getUsage()
            , "resultsQuality"      => $this->app->getResultsQuality()
            , "presentability"      => $this->app->getPresentability()
            , "minimumOs"           => $this->app->getMinimumOsVersion()
            , "urls"                => $this->app->getUrls()
            , "icon_url"            => $this->app->getIconUrl()
            , "iosPrivacy"          => $this->app->getIosPrivacy()
            , "unavailable"         => null === $this->app->getReleaseDate()
            , "developer"           => $this->app->getDeveloper()
            , "storeUrl"            => $this->app->getStoreUrl()
            , "downloadApp"         => $this->app->getName() . " herunterladen"
        ]);
    }

    /**
     * @param DateTimeInterface|null $dateTime
     *
     * @return string|null
     */
    private function getDateDescription(?DateTimeInterface $dateTime): ?string {
        if (null === $dateTime) return null;
        if ($dateTime->getTimestamp() < 10) return null;

        $pattern = $this->environment->getGermanDatePattern();
        return $dateTime->format((string) $pattern);
    }

    /**
     * @return array
     */
    private function getTimestampDescriptions(): array {
        $arr = [];

        $insertDate       = $this->getDateDescription($this->app->getCreateTs());
        $lastUserUpdateTs = $this->getDateDescription($this->app->getLastUserUpdateTs());
        $lastUpdated      = $this->getDateDescription($this->app->getLastUpdated());

        if (null !== $insertDate) {
            $arr["Datenbankeintrag"] = $insertDate;
        }
        if (null !== $lastUserUpdateTs) {
            $arr["Datenbank-Update"] = $lastUserUpdateTs;
        }
        if (null !== $lastUpdated) {
            $arr["App-Store-Abgleich"] = $lastUpdated;
        }

        return $arr;
    }

    /**
     * @param int $operatingSystem
     *
     * @return string
     */
    private function getAppStoreIcon(int $operatingSystem): string {
        $baseUrl = Didapptic::getBaseURL(true);
        if (1 === $operatingSystem) {
            return $baseUrl . "/v1/resources/img/apple_logo_black.jpg/image/";
        }
        return $baseUrl . "/v1/resources/img/androidlogo.png/image/";
    }

    protected function onDestroy(): void {

    }

}
