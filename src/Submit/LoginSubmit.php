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

use DateTime;
use Didapptic\Object\User;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Application\I18N\TranslationService;
use Didapptic\Service\Session\SessionService;
use Didapptic\Service\User\Login\LoginService;
use Didapptic\Service\User\UserService;

class LoginSubmit extends AbstractSubmit {

    /** @var UserService|null $userService */
    private $userService = null;
    /** @var SessionService|null $sessionService */
    private $sessionService = null;

    /** @var User */
    private $user;
    /** @var User */
    private $backendUser;
    /** @var TranslationService */
    private $translationService;
    /** @var UserRepository */
    private $userManager;
    /** @var LoginService */
    private $loginService;

    public function __construct(
        UserService $userService
        , SessionService $sessionService
        , TranslationService $translationService
        , UserRepository $userManager
        , LoginService $loginService
    ) {
        parent::__construct();

        $this->userService        = $userService;
        $this->sessionService     = $sessionService;
        $this->translationService = $translationService;
        $this->userManager        = $userManager;
        $this->loginService       = $loginService;
    }

    protected function valid(): bool {
        return true;
    }

    protected function onCreate(): void {
        $parameters        = $this->getArguments();
        $this->user        = $this->userService->toUser($parameters);
        $this->backendUser = $this->userManager->getUser($parameters["name"]);
    }

    protected function create(): bool {

        if (null === $this->backendUser) {
            $this->addResponse(
                "user_authentication"
                , $this->translationService->translate("Der angegebene Benutzer konnte nicht gefunden werden. Bitte versuchen Sie es erneut.")
            );
            return false;
        }

        $verified = $this->loginService->verifyUser($this->user, $this->backendUser);

        if (true === $verified) {
            $this->sessionService->set("user_id", (string) $this->backendUser->getId());
            $this->sessionService->set("last_access", (string) (new DateTime())->getTimestamp());
            return true;
        }

        $this->addResponse(
            "user_authentication"
            , $this->translationService->translate("Der Benutzername oder das Passwort ist falsch. Bitte versuchen Sie es erneut.")
        );
        return false;
    }

    protected function onDestroy(): void {
        // silence is golden
    }

}
