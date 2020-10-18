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

const KEYCODE_ENTER = 13;

export class Material {

    constructor(
        fetcher
        , routes
        , stringLoader
        , modal
        , templateParser
        , templateLoader
    ) {
        this.fetcher = fetcher;
        this.routes = routes;
        this.stringLoader = stringLoader;
        this.modal = modal;
        this.templateParser = templateParser;
        this.templateLoader = templateLoader;

        this.deleteSingleButtonClicked = false;
        this.deleteButtonClicked = false;
        this.addButtonClicked = false;
        this.passwordCheckFired = false;
    }


    async run() {
        $('#dd__date__input').datepicker({dateFormat: 'dd.mm.yy'});
        $(".single_material").on("click", function (ev) {
            return true;
        });

        this.strings = await this.stringLoader.read();
        this.templates = await this.templateLoader.read();
        await this.addDeleteSingleListener();
        await this.addDeleteListener();
        await this.addNewMaterialListener();
        await this.addDecryptMaterialListener();
    }

    async addDeleteListener() {
        const _this = this;
        let button = $(".dd__material__delete__button");

        // base case: button is not visible (e.g. due to missing permission)
        if (null === button || typeof button === "undefined") return;

        button.click(function (ev) {
            ev.preventDefault();
            if (true === _this.deleteButtonClicked) return;
            _this.deleteButtonClicked = true;
            let materialId = button.data("material-id");
            let strings = _this.strings["material"]["strings"];

            _this.fetcher.delete(
                _this.routes.getMaterialDelete(materialId)
                , {}
                , (data, textStatus, jQxhr) => {
                    let obj = JSON.parse(data);
                    let responseCode = obj['response_code'];
                    let modalTitle = null;
                    let modalContent = null;
                    let success = false;

                    if (RESPONSE_CODE_OK === responseCode) {
                        modalTitle = strings["deleteModalTitleSuccess"];
                        modalContent = strings["deleteModalContentSuccess"];
                        success = true;
                    } else
                        if (RESPONSE_CODE_NOT_OK === responseCode) {
                            modalTitle = strings["deleteModalTitleError"];
                            modalContent = strings["deleteModalContentError"];
                        }

                    _this.modal.small(modalTitle, modalContent);

                    _this.deleteButtonClicked = false;

                    if (false === success) return;

                    window.setTimeout(function () {
                        location.reload();
                    }, 3000);
                }
                , (jqXhr, textStatus, errorThrown) => {
                    _this.deleteButtonClicked = false;

                    _this.modal.small(
                        strings["deleteModalTitleError"]
                        , strings["deleteModalContentError"]
                    );

                }
            );

        });
    }

    async addDeleteSingleListener() {
        const _this = this;
        let button = $(".dd__material__delete__single__button");

        // base case: button is not visible (e.g. due to missing permission)
        if (null === button || typeof button === "undefined") return;

        button.click(function (ev) {
            ev.preventDefault();
            if (true === _this.deleteSingleButtonClicked) return;
            _this.deleteSingleButtonClicked = true;
            let fileId = button.attr("data-file-id");
            let materialId = button.attr("data-material-id");
            let strings = _this.strings["material"]["strings"];

            _this.fetcher.delete(
                _this.routes.getMaterialDeleteSingle(fileId, materialId)
                , {}
                , (data, textStatus, jQxhr) => {
                    let obj = JSON.parse(data);
                    let responseCode = obj['response_code'];
                    let modalTitle = null;
                    let modalContent = null;
                    let success = false;

                    if (RESPONSE_CODE_OK === responseCode) {
                        modalTitle = strings["singleDeleteModalTitleSuccess"];
                        modalContent = strings["singleDeleteModalContentSuccess"];
                        success = true;
                    } else
                        if (RESPONSE_CODE_NOT_OK === responseCode) {
                            modalTitle = strings["singleDeleteModalTitleError"];
                            modalContent = strings["singleDeleteModalContentError"];
                        }

                    _this.modal.small(modalTitle, modalContent);

                    _this.deleteSingleButtonClicked = false;

                    if (false === success) return;

                    window.setTimeout(function () {
                        location.reload();
                    }, 3000);
                }
                , (jqXhr, textStatus, errorThrown) => {
                    _this.deleteSingleButtonClicked = false;

                    _this.modal.small(
                        strings["singleDeleteModalTitleError"]
                        , strings["singleDeleteModalContentError"]
                    );

                }
            );

        });
    }

    async addNewMaterialListener() {
        const _this = this;

        $("#dd__material__new__form").ajaxForm({
            url: _this.routes.getMaterialUpload()
            , dataType: 'html',

            beforeSubmit: function (arr, $form, options) {
                if (true === _this.addButtonClicked) return false;
                _this.addButtonClicked = true;
                let date = $("#dd__date__input").val();
                let errorMessages = [];
                let name = $("#dd__name__input").val();
                let password = $("#dd__password__input").val();
                let description = $("#dd__material__description").val();
                let documentCount = document.getElementById('dd__material__input__file').files.length;
                let strings = _this.strings["material"]["strings"];

                if (date === "") {
                    errorMessages.push(strings["newMaterialMissingDate"]);
                }
                if (name === "") {
                    errorMessages.push(strings["newMaterialMissingDate"]);
                }
                if (description === "") {
                    errorMessages.push(strings["newMaterialMissingContent"]);
                }
                if (documentCount === 0) {
                    errorMessages.push(strings["newMaterialMissingPropertiesTitle"]);
                }

                if (errorMessages.length > 0) {
                    _this.modal.small(
                        strings["newMaterialMissingPropertiesTitle"]
                        , errorMessages.join("<br>")
                    );
                    _this.addButtonClicked = false;
                    return false;
                }
                return true;
            },
            success: (data) => {
                _this.addButtonClicked = false;
                let strings = _this.strings["material"]["strings"];

                _this.modal.small(
                    strings["newMaterialSuccessTitle"]
                    , strings["newMaterialSuccessContent"]
                );

                window.setTimeout(function () {
                    location.reload();
                }, 3000);

            },
            error: function (jqXhr, textStatus, errorThrown) {
                _this.addButtonClicked = false;
                let strings = _this.strings["material"]["strings"];

                _this.modal.small(
                    strings["newMaterialErrorTitle"]
                    , strings["newMaterialErrorContent"]
                );

            }
        });
    }

    async addDecryptMaterialListener() {
        const _this = this;
        let input = $('.material--password--decrypt');

        input.keypress(
            (event) => {
                let keyCode = (event.keyCode ? event.keyCode : event.which);
                keyCode = parseInt(keyCode);
                let strings = _this.strings["material"]["strings"];
                let current = $(event.currentTarget);
                let spinner = current.next();

                if (KEYCODE_ENTER === keyCode) {
                    let materialId = current.attr("data-material-id");
                    let password = current.val();

                    if ("" === password) {
                        _this.modal.small(
                            strings["materialDecryptPasswordNoPasswordTitle"]
                            , strings["materialDecryptPasswordNoPasswordContent"]
                        )
                        return false;
                    }

                    if (true === _this.passwordCheckFired) return false;
                    _this.passwordCheckFired = true;
                    spinner.show();
                    current.attr("disabled", true);

                    _this.fetcher.post(
                        _this.routes.getDecryptMaterial()
                        , {
                            materialId: materialId
                            , password: password
                        }
                        , async (data, textStatus, jQxhr) => {
                            let obj = JSON.parse(data);
                            let responseCode = obj['response_code'];

                            if (RESPONSE_CODE_OK === responseCode) {

                                let compiled = await _this.templateParser.parse(
                                    _this.templates["material_templates"]
                                    , {
                                        files: obj["content"]["files"]["content"]
                                        , material_file_url: obj["content"]["material_file_url"]
                                        , can_delete_single_file: obj["content"]["can_delete_single_file"]
                                        , material_id: materialId
                                        , token: obj["content"]["token"]
                                    }
                                );

                                const uncle = current.parent().parent().next();

                                uncle.html(compiled)
                                uncle.fadeIn(
                                    () => {
                                        uncle.show()
                                    }
                                )
                                spinner.hide();
                                current.attr("disabled", false);

                            } else {
                                _this.modal.small(
                                    strings["materialDecryptPasswordIncorrectTitle"]
                                    , strings["materialDecryptPasswordIncorrectContent"]
                                )
                            }

                            _this.passwordCheckFired = false;
                        },
                        (jqXhr, textStatus, errorThrown) => {
                            _this.passwordCheckFired = false;
                            _this.modal.small(
                                strings["materialDecryptErrorTitle"]
                                , strings["materialDecryptErrorContent"]
                            )
                        }
                    );

                }
                event.stopPropagation();
            });
    }
}
