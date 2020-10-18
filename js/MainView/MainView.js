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
import {AppDetailModal} from "./AppDetail/AppDetailModal";
import {RESPONSE_CODE_NOT_OK, RESPONSE_CODE_OK} from "../Library/Backend/Fetcher";

export class MainView {
    constructor(
        templateLoader
        , stringLoader
        , templateParser
        , fetcher
        , routes
        , filter
        , integerService
        , modal
    ) {
        this.templateLoader = templateLoader;
        this.stringLoader = stringLoader;
        this.templateParser = templateParser;
        this.fetcher = fetcher;
        this.routes = routes;
        this.filter = filter;
        this.integerService = integerService;
        this.position = 0;
        this.modal = modal;
    }

    async run() {
        await this.onInfoButtonClick();
        await this.onAppClick();
        await this.loadAppsAsync();
        await this.filter.run();
    }

    async onAppClick() {
        const element = $("#appbox #app-wrapper");

        element.children(".ab-main").each((i, v) => {
            const box = $(v);
            box.off("click").on("click", (event) => {
                event.preventDefault();
                event.stopImmediatePropagation();
                const storeId = box.attr("data-store-id");

                this.fetcher.get(
                    this.routes.getAppDetail(storeId)
                    , {}
                    , async (x, y, z) => {

                        const modal = new AppDetailModal(
                            this.modal
                            , this.templateLoader
                            , this.stringLoader
                            , this.templateParser
                            , this.routes
                        );
                        await modal.run(JSON.parse(x));
                    }
                );
            });
        });
    }

    async loadAppsAsync() {
        const appBox = $("#appbox");
        const appWrapper = $("#app-wrapper");
        const spinner = $("#loading-spinner");
        let templates = await this.templateLoader.read();
        const _this = this;
        let chunkSize = appBox.data("chunk-size");
        chunkSize = _this.integerService.parse(chunkSize);
        _this.position = _this.integerService.parse(_this.position);

        spinner.addClass("d-flex");
        spinner.removeClass("d-none");

        _this.fetcher.get(
            _this.routes.getPreview(chunkSize)
            , {}
            , async (data) => {
                let object = JSON.parse(data);
                let responseCode = object["response_code"];

                if (RESPONSE_CODE_OK === responseCode) {
                    let data = object["content"]["data"];
                    
                    let template = templates['main_view_app_row'];
                    let rendered = await _this.templateParser.parse(
                        template
                        , data
                    )
                    chunkSize = _this.integerService.parse(data["number_of_apps"]);

                    appWrapper.append(rendered);
                    _this.position = (_this.position + chunkSize);
                    await _this.onAppClick();
                    await _this.filter.filter(chunkSize);
                }

                if (RESPONSE_CODE_NOT_OK === responseCode) {

                }

                spinner.removeClass("d-flex");
                spinner.addClass("d-none");

            }
        )
    }

    async onInfoButtonClick() {
        const infoButton = $("#main-info-button");
        const text = $("#filter-comment-text");
        let strings = await this.stringLoader.read();

        strings = strings["main"]["strings"];
        infoButton.on(
            "click"
            , () => {

                this.modal.small(
                    strings["infoTitle"]
                    , text.html()
                );

            });
    }

}
