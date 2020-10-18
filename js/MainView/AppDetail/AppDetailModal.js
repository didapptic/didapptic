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
const SLIDE_TOGGLE_DURATION = 500;

export class AppDetailModal {

    constructor(
        didappticModal
        , templateLoader
        , stringLoader
        , templateParser
        , routes
    ) {
        this.didappticModal = didappticModal;
        this.templateLoader = templateLoader;
        this.stringLoader = stringLoader;
        this.templateParser = templateParser;
        this.routes = routes;
    }

    async run(options) {
        const _this = this;
        const templates = await this.templateLoader.read();
        let strings = await this.stringLoader.read();
        strings = strings["app_detail"]["strings"];

        const template = await this.templateParser.parse(
            templates["app_detail"]
            , $.extend(strings, options)
        );

        await this.didappticModal.large(
            template
            , () => {
                const header = $(".app-detail-header");
                // const header = $(template).closest("#app-detail-header");

                // console.log($(template).find("#app-detail-header"));

                header.click(
                    () => {

                        const content = header.next();

                        content.slideToggle(
                            SLIDE_TOGGLE_DURATION
                            , () => {

                                header.text(
                                    () => {

                                        if (content.is(":visible")) {
                                            return strings["collapseLabel"];
                                        }

                                        return strings["expandLabel"];

                                    });
                            });

                    });

                $("#editButton").click(
                    (ev) => {
                        const storeId = $("#modal-header").data("store-id");
                        ev.preventDefault();
                        ev.stopImmediatePropagation();
                        window.location = _this.routes.getEditApp(storeId)
                    })

            }
        );


    }
}
