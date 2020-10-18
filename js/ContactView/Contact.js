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

export class Contact {

    constructor(
        fetcher
        , routes
        , stringLoader
        , modal
    ) {
        this.fetcher = fetcher;
        this.routes = routes;
        this.stringLoader = stringLoader;
        this.modal = modal;

        this.submitted = false;
    }

    async run() {
        let _this = this;
        let form = $("#contact-form");
        this.strings = await _this.stringLoader.read();
        let strings = _this.strings["contact"]["strings"];

        form.submit((e) => {
            e.preventDefault();
            if (true === _this.isSubmitted()) return false;
            _this.disableSubmit();

            // we are evaluating with bootstrap,
            // no need to check values here again!

            _this.fetcher.put(
                _this.routes.getNewContact()
                , {
                    name: $("#form_name").val()
                    , email: $("#form_email").val()
                    , message: $("#form_message").val()
                }
                , (data, textStatus, jQxhr) => {
                    let obj = jQuery.parseJSON(data);
                    let responseCode = obj["response_code"];
                    let title = null;
                    let content = null;

                    if (RESPONSE_CODE_OK === responseCode) {
                        title = strings["messageSentTitle"];
                        content = strings["messageSentContent"];
                    } else
                        if (RESPONSE_CODE_NOT_OK === responseCode) {
                            title = strings["messageNotSentTitle"];
                            content = strings["messageNotSentContent"];
                        } else {
                            // should never happen
                            return;
                        }

                    _this.modal.small(
                        title
                        , content
                    )

                    _this.enableSubmit();
                    _this.clearInputs();
                }
                , (jqXhr, textStatus, errorThrown) => {
                    _this.modal.small(
                        strings["messageNotSentTitle"]
                        , strings["messageNotSentContent"]
                    )
                    _this.enableSubmit();
                    _this.clearInputs();
                }
            )

        });
    }

    enableSubmit() {
        let that = this;
        window.setTimeout(
            function () {
                $("#dd__contact__submit__button").attr("disabled", false);
                that.submitted = false;
            }, 3000
        )
    }

    disableSubmit() {
        $("#dd__contact__submit__button").attr("disabled", true);
        this.submitted = true;
    }

    isSubmitted() {
        return this.submitted;
    }

    clearInputs() {
        $('#contact-form')[0].reset();
    }
}
