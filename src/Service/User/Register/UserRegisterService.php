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

namespace Didapptic\Service\User\Register;

use Didapptic\Object\Registrant;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\Email\EmailService;
use Didapptic\Service\User\UserService;

/**
 * Class UserRegisterService
 *
 * @package Didapptic\Service\User\Register
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class UserRegisterService {

    /** @var UserRepository */
    private $userManager;
    /** @var EmailService */
    private $emailService;
    /** @var UserService */
    private $userService;

    public function __construct(
        UserRepository $userManager
        , EmailService $emailService
        , UserService $userService
    ) {
        $this->userManager  = $userManager;
        $this->emailService = $emailService;
        $this->userService  = $userService;
    }

    public function registerUser(Registrant $registrant, string $confirmationToken): Registrant {

        $registrant = $this->validateRegistrant($registrant);

        if (false === $registrant->isValid()) {
            return $registrant;
        }

        if (strlen(trim($confirmationToken)) === 0) {
            $registrant->setStatus(Registrant::STATUS_TECHNICAL_TOKEN_ERROR);
            return $registrant;
        }

        $lastInsertId = $this->userManager->insert($registrant, $confirmationToken);

        if (null === $lastInsertId) {
            $registrant->setStatus(Registrant::STATUS_TECHNICAL_DB_ERROR);
            return $registrant;
        }

        $registrant->merge(
        /** @phpstan-ignore-next-line */
            $this->userManager->getUserById($lastInsertId)
        );
        return $registrant;
    }

    private function validateRegistrant(Registrant $registrant): Registrant {

        if ("" === $registrant->getFirstName()) {
            $registrant->setStatus(Registrant::STATUS_INVALID_FIRST_NAME);
            return $registrant;
        }
        if ("" === $registrant->getLastName()) {
            $registrant->setStatus(Registrant::STATUS_INVALID_LAST_NAME);
            return $registrant;
        }
        if ("" === $registrant->getName()) {
            $registrant->setStatus(Registrant::STATUS_INVALID_LAST_NAME);
            return $registrant;
        }
        if (false === $this->emailService->validEmailAddress($registrant->getEmail())) {
            $registrant->setStatus(Registrant::STATUS_INVALID_EMAIL);
            return $registrant;
        }
        if ("" === $registrant->getPlainPassword()) {
            $registrant->setStatus(Registrant::STATUS_INVALID_PASSWORD);
            return $registrant;
        }

        if ($registrant->getPlainPassword() !== $registrant->getPlainPasswordRepeat()) {
            $registrant->setStatus(Registrant::STATUS_INVALID_PASSWORD_EQUALS_PASSWORD_REPEAT);
            return $registrant;
        }

        if (false === $this->userService->isStrongPassword($registrant->getPassword())) {
            $registrant->setStatus(Registrant::STATUS_INVALID_PASSWORD_STRENGTH);
            return $registrant;
        }

        if (count($registrant->getSubjects()) < 1) {
            $registrant->setStatus(Registrant::STATUS_INVALID_SUBJECTS);
            return $registrant;
        }

        $emailExists    = $this->userManager->emailExists($registrant->getEmail());
        $usernameExists = $this->userManager->userNameExists($registrant->getName());

        if (true === $emailExists) {
            $registrant->setStatus(Registrant::STATUS_INVALID_EMAIL_ALREADY_REGISTERED);
            return $registrant;
        }
        if (true === $usernameExists) {
            $registrant->setStatus(Registrant::STATUS_INVALID_USERNAME_ALREADY_REGISTERED);
            return $registrant;
        }


        return $registrant;
    }

    public function generateToken(): string {
        return (bin2hex(random_bytes(50)));
    }

    public function toRegistrant(array $array): Registrant {
        $firstName       = isset($array["first_name"]) ? $array["first_name"] : "";
        $lastName        = isset($array["last_name"]) ? $array["last_name"] : "";
        $userName        = isset($array["username"]) ? $array["username"] : "";
        $password        = isset($array["password"]) ? $array["password"] : "";
        $passwordRepeat  = isset($array["password_repeat"]) ? $array["password_repeat"] : "";
        $email           = isset($array["email"]) ? $array["email"] : "";
        $websiteURL      = isset($array["website_url"]) ? $array["website_url"] : "";
        $subjects        = isset($array["subjects"]) ? $array["subjects"] : [];
        $wantsNewsletter = (isset($array["wants_newsletter"]) && $array["wants_newsletter"] === "true")
            ? true : false;

        $registrant = new Registrant();
        $registrant->setFirstName($firstName);
        $registrant->setLastName($lastName);
        $registrant->setName($userName);
        $registrant->setEmail($email);
        $registrant->setPassword($password);
        $registrant->setPasswordRepeat($passwordRepeat);
        $registrant->setPlainPassword($password);
        $registrant->setPlainPasswordRepeat($passwordRepeat);
        $registrant->setSubjects($subjects);
        $registrant->setWebsiteURL($websiteURL);
        $registrant->setWantsNewsletter($wantsNewsletter);

        return $registrant;
    }

}
