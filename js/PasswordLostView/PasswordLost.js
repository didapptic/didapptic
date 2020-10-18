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
import {RESPONSE_CODE_OK} from "../Library/Backend/Fetcher";

export class PasswordLost {

    constructor(
        stringService
        , stringLoader
        , modal
        , fetcher
        , routes
    ) {
        this.stringService = stringService;
        this.stringLoader = stringLoader;
        this.modal = modal;
        this.fetcher = fetcher;
        this.routes = routes;
    }

    async run() {
        const _this = this;
        let strings = await _this.stringLoader.read();
        strings = strings["password_lost"]["strings"];

        $('#reset-pw-form').submit(function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            let userName = $("#userName").val();

            if (true === _this.stringService.isEmpty(userName)) {
                _this.modal.small(
                    strings["noValueProvidedTitle"]
                    , strings["noValueProvidedContent"]
                )
                return;
            }

            _this.fetcher.put(
                _this.routes.getPasswordLostSubmit()
                , {
                    "username": userName
                }
                , (x, y, z) => {
                    let obj = jQuery.parseJSON(x);
                    let responseCode = obj["response_code"];
                    let message = [];

                    if (RESPONSE_CODE_OK === responseCode) {
                        message.push(strings["resultSuccessContent"]);
                    } else {
                        message.push(strings["resultErrorContent"]);
                    }

                    _this.modal.small(
                        strings["resultTitle"]
                        , message.join("<br>")
                    )

                }
                , (jqXhr, textStatus, errorThrown) => {
                    _this.modal.small(
                        strings["resultErrorTitle"]
                        , strings["resultError"]
                    )
                }
            );

        });

    }
}
