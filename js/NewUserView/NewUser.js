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
import {RESPONSE_CODE_NOT_OK, RESPONSE_CODE_OK} from "../Library/Backend/Fetcher";

const STATUS_VALID = 0;
const STATUS_INVALID_FIRST_NAME = 1;
const STATUS_INVALID_LAST_NAME = 2;
const STATUS_INVALID_EMAIL = 3;
const STATUS_INVALID_PASSWORD = 4;
const STATUS_INVALID_PASSWORD_EQUALS_PASSWORD_REPEAT = 5;
const STATUS_INVALID_PASSWORD_STRENGTH = 6;
const STATUS_INVALID_SUBJECTS = 7;
const STATUS_INVALID_EMAIL_ALREADY_REGISTERED = 8;
const STATUS_INVALID_USERNAME_ALREADY_REGISTERED = 9;
const STATUS_WP_COULD_NOT_BE_CREATED = 10;
const STATUS_INVALID_USER_NAME = 11;

export class NewUser {

    constructor(
        stringService
        , stringLoader
        , passwordService
        , emailService
        , urlService
        , modal
        , fetcher
        , routes
        , environment
        , router
    ) {
        this.stringService = stringService;
        this.stringLoader = stringLoader;
        this.passwordService = passwordService;
        this.emailService = emailService;
        this.urlService = urlService;
        this.modal = modal;
        this.fetcher = fetcher;
        this.routes = routes;
        this.environment = environment;
        this.router = router;
    }

    async run() {
        const _this = this;
        let unchecked = true;
        let strings = await _this.stringLoader.read();
        strings = strings["new_user"]["strings"];

        $(".subject-checkbox").click(function () {
            let value = $(this).val();
            if (value === "-1") {
                if (unchecked) {
                    $("#subjects-checkboxes").find('input[type="checkbox"]').prop('checked', true);
                    unchecked = false;
                } else {
                    $("#subjects-checkboxes").find('input[type="checkbox"]').prop('checked', false);
                    unchecked = true;
                }
            }
        });

        $('#new-user-form').submit(async (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();

            let errorMessages = [];
            let firstName = $("#first-name").val();
            let lastName = $("#last-name").val();
            let userName = $("#user-name").val();
            let password = $("#password").val();
            let passwordRepeat = $("#password-repeat").val();
            let eMail = $("#e-mail").val();
            let websiteURL = $("#website-url").val();
            let newsletter = $("#newsletter").is(":checked");
            let subjectsCheckboxes = [];

            $('#subjects-checkboxes input:checked').each(function () {
                if ($(this).attr('value') !== "-1")
                    subjectsCheckboxes.push($(this).attr('value'));
            });

            if (true === _this.stringService.isEmpty(firstName)) {
                errorMessages.push(strings["invalidFirstName"]);
            }
            if (true === _this.stringService.isEmpty(lastName)) {
                errorMessages.push(strings["invalidLastName"]);
            }
            if (true === _this.stringService.isEmpty(userName)) {
                errorMessages.push(strings["invalidUserName"]);
            }
            if (true === _this.stringService.isEmpty(password)) {
                errorMessages.push(strings["invalidPassword"]);
            }
            if (true === _this.stringService.isEmpty(passwordRepeat)) {
                errorMessages.push(strings["invalidPasswordRepeat"]);
            }
            if (false === _this.passwordService.isStrongPassword(password)) {
                errorMessages.push(strings["weakPassword"]);
            }
            if (false === _this.stringService.equals(password, passwordRepeat)) {
                errorMessages.push(strings["passwordsNotMatch"]);
            }
            if (false === _this.emailService.isEmail(eMail)) {
                errorMessages.push(strings["invalidEmail"]);
            }
            if ("" !== websiteURL && false === _this.urlService.validURL(websiteURL)) {
                errorMessages.push(strings["invalidURL"]);
            }
            if (subjectsCheckboxes.length === 0) {
                errorMessages.push(strings["invalidSubject"]);
            }

            if (errorMessages.length > 0) {
                _this.modal.small(
                    strings["errorTitle"]
                    , errorMessages.join("<br>")
                )
                return;
            }


            _this.fetcher.put(
                _this.routes.getNewUserSubmit()
                , {
                    "first_name": _this.stringService.trim(firstName)
                    , "last_name": _this.stringService.trim(lastName)
                    , "username": _this.stringService.trim(userName)
                    , "password": _this.stringService.trim(password)
                    , "password_repeat": _this.stringService.trim(passwordRepeat)
                    , "email": _this.stringService.trim(eMail)
                    , "website_url": _this.stringService.trim(websiteURL)
                    , "wants_newsletter": newsletter
                    , "subjects": subjectsCheckboxes
                }
                , async (x, y, z) => {
                    let obj = jQuery.parseJSON(x);
                    let responseCode = obj["response_code"];
                    let registerCode = obj["content"]["register_code"];

                    if (RESPONSE_CODE_OK === responseCode && STATUS_VALID === registerCode) {

                        _this.modal.small(
                            strings["userRegisteredTitle"]
                            , strings["userRegisteredContent"]
                        );

                        if (false === _this.environment.isDebug()) {
                            _this.router.routeToLoginPage(3000);
                        }
                    }

                    if (RESPONSE_CODE_NOT_OK === responseCode) {

                        let description = await _this.getErrorDescription(registerCode);
                        _this.modal.small(
                            strings["userRegisterErrorTitle"]
                            , description
                        )
                    }

                }
                , async (x, y, z) => {

                    _this.modal.small(
                        strings["userRegisterErrorTitle"]
                        , strings["userRegisterError"]
                    )

                }
                , async (x, y, z) => {
                    if (true === _this.environment.isDebug()) return;
                    $("body").addClass('loading');
                }
                , async (x, y, z) => {
                    if (true === _this.environment.isDebug()) return;
                    $("body").removeClass('loading');
                }
            )

        });
    }

    async getErrorDescription(responseCode) {
        let strings = await this.stringLoader.read();
        strings = strings["new_user"]["strings"];
        // default
        let description = strings["userRegisteredContent"];

        switch (responseCode) {
            case STATUS_INVALID_FIRST_NAME:
                description = strings["userRegisteredInvalidFirstName"];
                break;
            case STATUS_INVALID_LAST_NAME:
                description = strings["userRegisteredInvalidLastName"];
                break;
            case STATUS_INVALID_EMAIL:
                description = strings["userRegisteredInvalidEmail"];
                break;
            case STATUS_INVALID_PASSWORD:
                description = strings["userRegisteredInvalidPassword"];
                break;
            case STATUS_INVALID_PASSWORD_EQUALS_PASSWORD_REPEAT:
                description = strings["userRegisteredInvalidPasswordEqualsRepeat"];
                break;
            case STATUS_INVALID_PASSWORD_STRENGTH:
                description = strings["userRegisteredInvalidPasswordStrength"];
                break;
            case STATUS_INVALID_SUBJECTS:
                description = strings["userRegisteredInvalidSubjects"];
                break;
            case STATUS_INVALID_EMAIL_ALREADY_REGISTERED:
                description = strings["userRegisteredInvalidAlreadyExists"];
                break;
            case STATUS_INVALID_USERNAME_ALREADY_REGISTERED:
                description = strings["userRegisteredInvalidUserNameAlreadyExists"];
                break;
            case STATUS_WP_COULD_NOT_BE_CREATED:
                description = strings["userRegisteredWpCouldNotBeCreated"];
                break;
            case STATUS_INVALID_USER_NAME:
                description = strings["userRegisteredInvalidUserName"];
                break;
        }

        return description
    }
}
