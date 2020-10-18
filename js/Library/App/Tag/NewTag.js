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
import {RESPONSE_CODE_NOT_OK, RESPONSE_CODE_OK} from "../../Backend/Fetcher";

export class NewTag {

    constructor(
        modal
        , stringLoader
        , templateLoader
        , templateParser
        , fetcher
        , route
    ) {
        this.modal = modal;
        this.stringLoader = stringLoader;
        this.templateLoader = templateLoader;
        this.templateParser = templateParser;
        this.fetcher = fetcher;
        this.route = route;
        this.buttonContent = null;
    }

    run() {
        const _this = this;
        $("#new__tag").on("click", async () => {
            const templates = await _this.templateLoader.read();
            const newTagTemplate = templates["new_tag"];
            let strings = await _this.stringLoader.read();
            strings = strings["new_tag"]["strings"];

            const content = await _this.templateParser.parse(
                newTagTemplate
                , strings["formContent"]
            );

            _this.modal.prompt(
                strings["tagTitle"]
                , content
                , strings["tagPositive"]
                , strings["tagNegative"]
                , (button) => {
                    _this.registerButtonClickListener(button);
                }
            )
        })
    }

    registerButtonClickListener(button) {
        const _this = this;

        const tag = $("#tag");
        const tagHelp = $("#tagHelp");

        const existingTags = [];

        $("form#new-app-form :input.tag-checkbox").each(
            (i, v) => {
                existingTags.push($(v).data("name").toLowerCase());
            }
        );

        _this.lockButton(button);

        tag.off("keyup").on("keyup", () => {

            _this.unlock(
                tag
                , tagHelp
                , button
            );

            const candidate = tag.val().toLowerCase();
            const tagExists = $.inArray(candidate, existingTags) !== -1;

            if (true === tagExists) {
                _this.lock(
                    tag
                    , tagHelp
                    , button
                );
            }

            if ("" === candidate.trim()) {
                _this.lockButton(button);
            }

        });

        button.off("click").on("click", () => {
            const candidate = tag.val();
            _this.fetcher.post(
                _this.route
                , {
                    value: candidate
                }
                , (data) => {
                    data = JSON.parse(data)
                    const responseCode = data["response_code"];
                    const id = data["content"]["id"];

                    if (RESPONSE_CODE_OK === responseCode) {
                        _this.showLabel($("#tagAdded"), false);
                        $("#tag-checkboxes").append('<div class="col-xl-4"><div class="checkbox"><label><input type="checkbox" value="' + id + '" class="tag-checkbox" style="margin-right: 5px;" data-name="' + candidate + '">' + candidate + '</label></div></div>');
                        _this.registerButtonClickListener(button)
                        tag.val("");
                    } else
                        if (RESPONSE_CODE_NOT_OK === responseCode) {
                            _this.showLabel($("#tagNotAdded"));
                        }
                }
                , (data) => {
                    _this.showLabel($("#tagNotAdded"));
                }
                , () => {
                    _this.lockButton(button)
                    _this.addSpinner(button)
                }
                , () => {
                    _this.unlockButton(button)
                    _this.removeSpinner(button)
                }
            );
        });
    }

    lock(tag, tagHelp, button) {
        tag.addClass("is-invalid");
        this.showLabel(tagHelp);
        this.lockButton(button);
    }

    lockButton(button) {
        button.prop("disabled", true);
    }

    unlock(tag, tagHelp, button) {
        tag.removeClass("is-invalid");
        this.hideLabel(tagHelp);
        this.unlockButton(button);
    }

    unlockButton(button) {
        button.prop("disabled", false);
    }

    showLabel(label, keep = true) {
        const _this = this;
        label.removeClass("d-sm-none");
        if (true === keep) return;
        label.fadeOut(6000, () => {
            _this.hideLabel(label)
        });
    }

    hideLabel(label) {
        label.addClass("d-sm-none")
    }

    addSpinner(button) {
        this.buttonContent = button.html();
        button.html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> wird gespeichert ...`
        );
    }

    removeSpinner(button) {
        if (null === this.buttonContent) return
        button.html(
            this.buttonContent
        )
    }
}
