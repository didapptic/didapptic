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
use Didapptic\Backend\Processor;
use Didapptic\Didapptic;
use Didapptic\Object\Constant\CSS;
use Didapptic\Object\Constant\HTTP;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Environment;
use Didapptic\Repository\MenuRepository;
use Didapptic\Service\Application\Menu\MenuService;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\UserService;
use doganoo\SimpleRBAC\Handler\PermissionHandler;
use GuzzleHttp\Client;
use Twig\Environment as TwigEnv;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use function array_merge;
use function in_array;

/**
 * Class AbstractController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
abstract class AbstractController {

    /** @var null|string $templatePath */
    private $templatePath;
    /** @var string $pageTitle */
    private $pageTitle;
    /** @var array|null $javaScript */
    private $javaScript;
    /** @var array|null $css */
    private $css;
    /** @var Environment $properties */
    private $properties;
    /** @var PermissionHandler */
    private $permissionHandler;
    /** @var PermissionService */
    private $permissionService;
    /** @var null|UserService */
    private $userService;
    /** @var null|MenuService $menuService */
    private $menuService;
    /** @var array */
    private $arguments;
    /** @var int */
    private $responseCode;
    /** @var Client */
    private $guzzleClient;
    /** @var Processor */
    private $processor;
    /** @var MenuRepository */
    private $menuManager;
    /** @var Environment */
    private $environment;

    /**
     * AbstractController constructor.
     *
     * @param string $pageTitle
     */
    public function __construct(string $pageTitle = "") {
        $this->userService       = Didapptic::getServer()->query(UserService::class);
        $this->menuService       = Didapptic::getServer()->query(MenuService::class);
        $this->permissionHandler = Didapptic::getServer()->query(PermissionHandler::class);
        $this->permissionService = Didapptic::getServer()->query(PermissionService::class);
        $this->guzzleClient      = Didapptic::getServer()->query(Client::class);
        $this->processor         = Didapptic::getServer()->query(Processor::class);
        $this->menuManager       = Didapptic::getServer()->query(MenuRepository::class);
        $this->environment       = Didapptic::getServer()->query(Environment::class);

        $this->properties   = Didapptic::getServer()->query(Environment::class);
        $this->templatePath = Didapptic::getServer()->getTemplatePath();
        $this->pageTitle    = $pageTitle;
        $this->javaScript   = [];
        $this->css          = [];

        $this->setResponseCode(HTTP::OK);

        $this->registerCss(CSS::JQUERY_UI);
        $this->registerCss(CSS::BOOTSTRAP_SELECT);
        $this->registerCss(CSS::BOOTSTRAP);
        $this->registerCss(CSS::COMMON);
        $this->registerCss(CSS::MAIN_CSS);

        $this->css[] = "https://use.fontawesome.com/releases/v5.3.1/css/all.css";

    }

    /**
     * @param string $name
     */
    protected function registerCss(string $name) {
        $baseURL = Didapptic::getBaseURL(false);
        $path    = $baseURL . "dist/css/$name.css";
        if (!in_array($path, $this->css)) {
            $this->css[] = $path;
        }
    }

    public function getArgument(string $name): ?string {
        $value = $this->arguments[$name] ?? null;
        if (null === $value) return $value;
        return (string) $value;
    }

    public function getArguments(): array {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void {
        foreach ($arguments as $index => $argument) {
            $arguments[$index] = htmlspecialchars($argument);
        }
        $this->arguments = $arguments;
    }

    public function run(bool $contentOnly = false): string {

        if (true === $contentOnly) {
            $html = "";
            $this->onCreate();
            $html .= $this->create();
            $this->onDestroy();
            return $html;
        }

        $html = "";
        $html .= $this->loadHeader(false);
        $this->onCreate();
        $html .= $this->create();
        $this->onDestroy();
        $html .= $this->loadFooter();

        return $html;
    }

    protected abstract function onCreate(): void;

    protected abstract function create(): ?string;

    protected abstract function onDestroy(): void;

    /**
     * @param bool $contentOnly
     *
     * @return string
     */
    private function loadHeader(bool $contentOnly) {
        $self = Didapptic::getBaseURL(true);

        $user = $this->userService->getUser();
        $menu = $this->menuManager->getMenu();
        $menu = $this->menuService->prepareMenu(
            $menu
            , null !== $user
        );

        $headHtml    = $this->loadHead($contentOnly);
        $template    = $this->loadTemplate($this->getTemplatePath(), View::HEADER_VIEW);
        $debug       = $this->properties->isDebug();
        $production  = $this->properties->isProduction();
        $description = $this->environment->getEnvironmentDescription();

        return $template->render([
            "head"                    => $headHtml,
            "host"                    => $self,
            "debug"                   => $debug,
            "selfPath"                => $self,
            "logoPath"                => $self . "/v1/resources/img/logo.png/image/",
            "pageNavigation"          => $menu,
            "appName"                 => Didapptic::APP_NAME,
            "production"              => $production,
            "environment_description" => $description,
        ]);
    }

    /**
     * @param bool $contentOnly
     *
     * @return string
     */
    private function loadHead(bool $contentOnly): string {
        $self      = Didapptic::getBaseURL(true);
        $pageTitle = Didapptic::APP_NAME;

        $template = $this->loadTemplate(
            $this->getTemplatePath()
            , View::HEAD
        );

        $css        = [];
        $javascript = $this->javaScript;
        if (!$contentOnly) {
            $css = array_merge($css, $this->css);
        }

        if ("" !== $this->pageTitle) {
            $pageTitle = $this->pageTitle . " | " . $pageTitle;
        }

        return $template->render([
            "siteLogoPath" => $self . "/v1/resources/img/sitelogo.png/image/",
            "faviconPath"  => $self . "/v1/resources/img/favicon.png/image/",
            "jsArray"      => $javascript,
            "cssArray"     => $css,
            "pageTitle"    => $pageTitle,
        ]);
    }

    protected final function loadTemplate($path, $name) {
        $loader = new FilesystemLoader(
            Didapptic::getServer()->getTemplatePath()
        );
        $twig   = new TwigEnv($loader, []);
        $twig->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));
        return $twig->load($name);
    }

    /**
     * @return string|null
     * @deprecated
     */
    protected final function getTemplatePath() {
        return $this->templatePath;
    }

    private function loadFooter() {
        $template = $this->loadTemplate($this->getTemplatePath(), "footer.twig");
        $self     = Didapptic::getBaseURL(true);
        return $template->render([
            "appName"                  => Didapptic::APP_NAME
            , "copyrightLink"          => "https://www.camuka.de"
            , "copyrightName"          => "Ahmet Camuka"
            , "currentYear"            => (new DateTime())->format("Y")
            , "appDescription"         => "didapptic ist eine nicht-kommerzielle, didaktische App-Datenbank!"
            , "supporterTextBeginning" => "Unterstützt durch den"
            , "supporterUrl"           => "https://www.uni-frankfurt.de/44259890/Fachdidaktik"
            , "supporterTextInner"     => "Schwerpunkt Kunstdidaktik des Instituts für Kunstpädagogik der Goethe-Universität Frankfurt am Main"
            , "imprintLabel"           => "Impressum"
            , "imprintURL"             => $self . "/menu/imprint/"
            , "privacyLabel"           => "Datenschutz"
            , "privacyURL"             => $self . "/menu/privacy/"
            , "links"                  => [
                $self . "/menu/imprint/"   => "Impressum"
                , $self . "/menu/privacy/" => "Datenschutz"
                , $self . "/menu/partner/" => "Partner"
            ]
            , "builtBy"                => "entwickelt von"
            , "builtByHref"            => "https://www.dogan-ucar.de"
            , "builtByName"            => "Doğan Uçar"
        ]);
    }

    public function getResponseCode(): int {
        return $this->responseCode;
    }

    protected function setResponseCode(int $responseCode): void {
        $this->responseCode = $responseCode;
    }

    /**
     * @param string $name
     */
    protected function registerJavaScript(string $name): void {
        $baseURL = Didapptic::getBaseURL(false);
        $path    = $baseURL . "dist/js/$name.bundle.js";
        if (!in_array($path, $this->javaScript)) {
            $this->javaScript[] = $path;
        }
    }

    /**
     * @param int $permissionId
     *
     * @return bool
     */
    protected function hasPermission(int $permissionId) {
        return $this->permissionHandler->hasPermission($this->permissionService->toPermission($permissionId));
    }


}
