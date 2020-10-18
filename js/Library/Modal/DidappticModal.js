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
import 'bootstrap/dist/js/bootstrap'

export class DidappticModal {

    constructor(
        templateLoader
        , templateParser
    ) {
        this.templateLoader = templateLoader;
        this.templateParser = templateParser;
    }

    async large(
        content
        , onShown = () => {
        }
        , onHidden = () => {
        }
    ) {
        const templates = await this.templateLoader.read();
        const template = templates["modal_large"];

        const modal = await this.templateParser.parse(
            template
            , {
                content: content
            }
        );

        this.show(modal, onShown);
    }

    show(modal, onShown) {
        const modalObject = $(modal);

        modalObject.modal({
            keyboard: false
        })
        modalObject.on("shown.bs.modal", () => {
            onShown($("#dd__modal__promt__button"))
        })
        modalObject;
    }

    async prompt(
        title
        , message
        , positive = null
        , negative = null
        , onShown = () => {
        }
    ) {
        const templates = await this.templateLoader.read();
        const template = templates["modal_prompt"];

        const modal = await this.templateParser.parse(
            template
            , {
                title: title
                , message: message
                , positive: positive
                , negative: negative
            }
        );
        this.show(
            modal
            , onShown
        );
    }

    async small(
        title
        , message
        , onShown = () => {
        }
    ) {
        const templates = await this.templateLoader.read();
        const template = templates["modal_small"];

        const modal = await this.templateParser.parse(
            template
            , {
                title: title
                , message: message
            }
        );
        this.show(
            modal
            , onShown
        );
    }
}
