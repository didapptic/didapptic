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

const RETURN_CODE_OK = 0;
const RETURN_CODE_NO_USER_FOUND = 1;
const RETURN_CODE_USER_NOT_UPDATED = 2;
const RETURN_CODE_USER_USER_HAS_NO_WP = 3;
const RETURN_CODE_WP_NOT_UPDATED = 4;

export class ResetPassword {

    constructor(
        passwordService
        , stringLoader
        , modal
        , stringService
        , integerService
        , fetcher
        , routes
        , environment
        , router
    ) {
        this.passwordService = passwordService;
        this.stringLoader = stringLoader;
        this.stringService = stringService;
        this.integerService = integerService;
        this.modal = modal;
        this.fetcher = fetcher;
        this.routes = routes;
        this.environment = environment;
        this.router = router;
    }

    async run() {
        const _this = this;
        let strings = await _this.stringLoader.read();
        strings = strings["reset_password"]["strings"];

        $('#reset-password-form').submit(async (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();
            let password = $("#password").val();
            let user = $("#user").val();
            let token = $("#token").val();
            let message = [];

            if (false === _this.passwordService.isStrongPassword(password)) {
                message.push(strings["weakPassword"]);
            }

            if (true === _this.stringService.isEmpty(token)) {
                message.push(strings["noToken"]);
            }

            if (message.length > 0) {
                _this.modal.small(
                    strings["preErrorTitle"]
                    , message.join("<br>")
                )
                return
            }

            _this.fetcher.post(
                _this.routes.getPasswordUpdate()
                , {
                    "password": password
                    , "user": _this.integerService.parse(user)
                    , "token": _this.stringService.trim(token)
                }
                , (data) => {
                    let obj = jQuery.parseJSON(data);
                    let responseCode = obj["response_code"];
                    let returnCode = obj["content"]["return_code"];
                    let message = [];

                    if (RESPONSE_CODE_OK === responseCode) {

                        if (returnCode === RETURN_CODE_OK) {
                            message.push(strings["userUpdatedRegularly"]);
                        }
                        if (returnCode === RETURN_CODE_USER_USER_HAS_NO_WP) {
                            message.push(strings["userUpdatedButNoWp"]);
                        }

                        if (false === _this.environment.isDebug()) {
                            _this.router.routeToMainPage(10000);
                        }
                    }

                    if (RESPONSE_CODE_NOT_OK === responseCode) {
                        const description = _this.getResponseDescription(returnCode);
                        message.push(description);
                    }

                    _this.modal.small(
                        strings["updateOperationTitle"]
                        , message.join("<br>")
                    )

                }
            )

        });
    }

    async getResponseDescription(responseCode) {
        let strings = await this.stringLoader.read();
        strings = strings["password_lost"]["strings"];

        let description = strings["responseCodeDefault"];
        switch (responseCode) {
            case RETURN_CODE_NO_USER_FOUND:
                description = strings["noUserFound"];
                break;
            case RETURN_CODE_USER_NOT_UPDATED:
                description = strings["userNotUpdated"];
                break;
            case RETURN_CODE_WP_NOT_UPDATED:
                description = strings["userUpdatedButNotWp"];
                break;

        }
    }
}
