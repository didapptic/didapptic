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

use Didapptic\Object\Constant\CSS;
use Didapptic\Object\Constant\JavaScript;
use Didapptic\Object\Constant\View;
use Didapptic\Object\Environment;
use Didapptic\Object\Token;
use Didapptic\Repository\TokenRepository;
use Didapptic\Service\User\Register\TokenService;

/**
 * Class ResetPasswordViewController
 *
 * @package Didapptic\Controller
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class ResetPasswordViewController extends AbstractController {

    /** @var Token */
    private $token;
    private $templateName = View::RESET_PASSWORD_VIEW_ERROR;
    /** @var TokenRepository */
    private $tokenManager;
    /** @var Environment */
    private $environment;
    /** @var TokenService */
    private $tokenService;

    public function __construct(
        TokenRepository $tokenManager
        , Environment $environment
        , TokenService $tokenService
    ) {
        parent::__construct("Passwort zurücksetzen");
        $this->registerJavaScript(JavaScript::RESET_PASSWORD_SCRIPT);
        $this->registerCss(CSS::RESET_PASSWORD_CSS);
        $this->tokenManager = $tokenManager;
        $this->environment  = $environment;
        $this->tokenService = $tokenService;
    }

    protected function onCreate(): void {
        $this->token = $this->tokenManager->getToken(
            $this->getArgument("token")
        );
        $isOutdated  = $this->tokenService->isOutdated($this->token);

        if (false === $isOutdated && null !== $this->token) {
            $this->templateName = View::RESET_PASSWORD_VIEW;
        }

    }

    /**
     * @return string|null
     */
    protected function create(): ?string {

        $template = parent::loadTemplate(
            parent::getTemplatePath()
            , $this->templateName
        );

        $userId = null;
        if (null !== $this->token) {
            $userId = $this->token->getUser()->getId();
        }

        return $template->render([
            "title"                      => "Passwort zurücksetzen"
            , "description"              => "Bitte geben Sie ein neues Passwort ein"
            , "passwordLabel"            => "Passwort"
            , "userId"                   => $userId
            , "token"                    => $this->token
            , "passwordLabelPlaceholder" => "Passwort"
            , "resetButtonText"          => "Passwort zurücksetzen"
            , "errorDescription"         => "Der aufgerufene Link ist nicht mehr gültig, möglicherweise ist er abgelaufen. Bitte setzen Sie das Passwort erneut zurück und verfolgen die Anweisungen."
        ]);
    }

    protected function onDestroy(): void {

    }

}
