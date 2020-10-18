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

const RETURN_CODE_VALID = 0;
const RETURN_CODE_NAME_EXISTS = 1;
const RETURN_CODE_WEAK_PASSWORD = 2;
const RETURN_CODE_DB_ERROR = 3;

export class Profile {
    constructor(
        integerService
        , stringService
        , emailService
        , passwordService
        , fetcher
        , routes
        , modal
        , stringLoader
        , urlService
        , environment
    ) {
        this.integerService = integerService;
        this.stringService = stringService;
        this.emailService = emailService;
        this.passwordService = passwordService;
        this.fetcher = fetcher;
        this.routes = routes;
        this.modal = modal;
        this.stringLoader = stringLoader;
        this.urlService = urlService;
        this.environment = environment;
    }

    async run() {
        const _this = this;
        let strings = await _this.stringLoader.read();
        strings = strings["profile"]["strings"];

        $("#save__button__user").click(() => {
            let id = $("#profile-id").val();
            let name = $("#profile-name").val();
            let firstName = $("#profile-firstname").val();
            let lastName = $("#profile-lastname").val();
            let email = $("#profile-email").val();
            let passwordObject = $("#profile-password");
            let password = passwordObject.val();
            let website = $("#profile-website").val();
            let wpUserId = $("#profile-wp-userid").val();
            let newsLetter = $("#profile-newsletter").val();
            let rolesCheckboxes = [];
            let notificationCheckboxes = {};
            let messages = [];
            $('#roles-checkboxes input:checked').each(function () {
                let roleId = $(this).data('role-id');
                rolesCheckboxes.push(roleId);
            });
            $('#notification-checkboxes input:checked').each(function () {
                let typeId = $(this).data('type-id');
                let notificationId = $(this).data('notification-id');

                let types = notificationCheckboxes[notificationId] || [];
                types.push(typeId)
                notificationCheckboxes[notificationId] = types;
            });

            if (false === _this.integerService.isInteger(id)) {
                messages.push(strings["idNotGiven"]);
            }

            if (true === _this.stringService.isEmpty(name)) {
                messages.push(strings["nameNotGiven"]);
            }

            if (true === _this.stringService.isEmpty(firstName)) {
                messages.push(strings["firstNameNotGiven"]);
            }

            if (true === _this.stringService.isEmpty(lastName)) {
                messages.push(strings["lastNameNotGiven"]);
            }

            if (false === _this.emailService.isEmail(email)) {
                messages.push(strings["emailNotGiven"]);
            }

            if (false === _this.passwordService.isStrongPassword(password)) {
                messages.push(strings["weakPassword"]);
            }

            if ("" !== website && false === _this.urlService.validURL(website)) {
                messages.push(strings["notAUrl"]);
            }

            if (messages.length > 0) {
                _this.modal.small(
                    strings["profileSubmitErrorTitle"]
                    , messages.join("<br>")
                )
                return;
            }

            _this.fetcher.put(
                _this.routes.getUpdateUser()
                , {
                    "id": _this.integerService.parse(id)
                    , "name": _this.stringService.trim(name)
                    , "first_name": _this.stringService.trim(firstName)
                    , "last_name": _this.stringService.trim(lastName)
                    , "email": _this.stringService.trim(email)
                    , "password": password
                    , "website": _this.stringService.trim(website)
                    , "wp_user_id": _this.stringService.trim(wpUserId)
                    , "newsletter": _this.stringService.trim(newsLetter)
                    , "roles": rolesCheckboxes
                    , "notifications": notificationCheckboxes
                }
                , (x, y, z) => {
                    let object = JSON.parse(x);
                    let responseCode = object["response_code"];
                    let returnCode = object["content"]["return_code"];

                    if (
                        RESPONSE_CODE_OK === responseCode
                        && returnCode === RETURN_CODE_VALID
                    ) {
                        messages.push(strings["updatedSuccessContent"]);
                        if (false === _this.environment.isDebug()) {
                            passwordObject.val("");
                        }
                    }

                    if (RESPONSE_CODE_NOT_OK === responseCode
                    ) {
                        if (RETURN_CODE_NAME_EXISTS === returnCode) {
                            messages.push(strings["userNameExists"]);
                        }

                        if (RETURN_CODE_WEAK_PASSWORD === returnCode) {
                            messages.push(strings["weakPassword"]);
                        }

                        if (RETURN_CODE_DB_ERROR === returnCode) {
                            messages.push(strings["dbError"]);
                        }
                    }

                    _this.modal.small(
                        strings["afterUpdateTitle"]
                        , messages.join("<br>")
                    )
                }
                , (x, y, z) => {
                    _this.modal.small(
                        strings["afterUpdateErrorTitle"]
                        , strings["afterUpdateErrorContent"]
                    )
                }
                , (x, y, z) => {
                    $("body").addClass('loading');
                }
                , (x, y, z) => {
                    $("body").removeClass('loading');
                }
            )
        });

    }
}
