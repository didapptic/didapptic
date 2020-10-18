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

export class Login {

    constructor(
        fetcher
        , routes
        , router
        , modal
        , stringLoader
    ) {
        this.fetcher = fetcher;
        this.routes = routes;
        this.router = router;
        this.modal = modal;
        this.stringLoader = stringLoader;
    }

    async run() {
        const _this = this;
        const form = $("#login-form");
        const username = $("#userName");
        const password = $("#password");
        let strings = await _this.stringLoader.read();
        strings = strings["login"]["strings"];

        form.submit(async (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();

            let values = {
                "name": username.val()
                , "password": password.val()
            }

            _this.fetcher.put(
                _this.routes.getLoginSubmit()
                , values
                , (data, textStatus, jQxhr) => {
                    let object = JSON.parse(data);
                    let responseCode = object["response_code"];
                    let content = object["content"];
                    let messages = [];

                    if (RESPONSE_CODE_OK === responseCode) {
                        _this.router.routeToMainPage();
                        return;
                    } else
                        if (RESPONSE_CODE_NOT_OK === responseCode) {
                            messages.push(content["user_authentication"]);
                        }

                    _this.modal.small(
                        strings["modalErrorTitle"]
                        , messages.join("<br>")
                    )
                },
                (jqXhr, textStatus, errorThrown) => {
                    _this.modal.small(
                        strings["modalErrorTitle"]
                        , strings["modalBackendError"]
                    )
                }
            );

        });

    }
}
