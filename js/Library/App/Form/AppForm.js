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

export const MODE_EDIT = "edit.mode";
export const MODE_NEW = "new.mode";

const APP_TYPE_IOS = "ios";
const APP_TYPE_ANDROID = "android";
const APP_TYPE_UNDEFINED = "undefined";
const APP_TYPE_BOTH = "both";

export class AppForm {

    constructor(
        environment
        , fetcher
        , routes
        , modal
        , urlService
        , stringLoader
        , newSubject
        , newCategory
        , newTag
        , mode
        , floatService
        , stringService
        , integerService
    ) {
        this.environment = environment;
        this.fetcher = fetcher;
        this.routes = routes;
        this.modal = modal;
        this.urlService = urlService;
        this.stringLoader = stringLoader;
        this.newSubject = newSubject;
        this.newCategory = newCategory;
        this.newTag = newTag;
        this.mode = mode;
        this.floatService = floatService;
        this.stringService = stringService;
        this.integerService = integerService;
        this.appType = APP_TYPE_UNDEFINED;
    }

    async run() {
        const _this = this;
        let strings = await _this.getStrings();
        strings = strings["strings"];

        _this.listenForURL();

        _this.newSubject.run();
        _this.newCategory.run();
        _this.newTag.run();

        $('#new-app-form').submit(async (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();

            _this.determineAppType();

            let googleStoreUrl = $("#google-store-url").val();
            let iosStoreUrl = $("#ios-store-url").val();
            let iosPrivacy = $("#ios-privacy").val();
            let usage = $("#usage-range-slider").val();
            let resultsQuality = $("#results-quality-range-slider").val();
            let presentability = $("#presentability-range-slider").val();
            let didacticComment = $("#didactic-comment").val();
            let didacticRemark = $("#didactic-remark").val();
            let privacyComment = $("#privacy-comment").val();
            let privacy = $("#privacy option:selected").val();
            let author = $("#author option:selected").val();
            let recommendation = $("#recommendation option:selected").val();

            let subjectsCheckboxes = [];
            let categoryCheckboxes = [];
            let tagCheckboxes = [];
            let errorMessages = [];
            const isAndroid = _this.appType === APP_TYPE_ANDROID;
            const isApple = _this.appType === APP_TYPE_IOS;
            const isBoth = _this.appType === APP_TYPE_BOTH;
            const isAndroidOrBoth = true === isAndroid || true === isBoth;
            const isAppleOrBoth = true === isApple || true === isBoth;

            $('#subjects-checkboxes input:checked').each(function () {
                if ($(this).attr('value') !== "-1")
                    subjectsCheckboxes.push($(this).attr('value'));
            });
            $('#category-checkboxes input:checked').each(function () {
                categoryCheckboxes.push($(this).attr('value'));
            });
            $('#tag-checkboxes input:checked').each(function () {
                tagCheckboxes.push($(this).attr('value'));
            });

            if (
                false === _this.urlService.validURL(googleStoreUrl)
                && true === isAndroidOrBoth
            ) {
                errorMessages.push(strings["invalidGoogleUrl"]);
            }

            if (
                false === _this.urlService.validURL(iosStoreUrl)
                && true === isAppleOrBoth
            ) {
                errorMessages.push(strings["invalidAppleUrl"]);
            }

            if (false === _this.floatService.isFloat(usage)) {
                errorMessages.push(strings["invalidUsageValue"]);
            }
            if (false === _this.floatService.isFloat(resultsQuality)) {
                errorMessages.push(strings["invalidResultsQualityValue"]);
            }
            if (false === _this.floatService.isFloat(presentability)) {
                errorMessages.push(strings["invalidPresentabilityValue"]);
            }
            if (true === _this.stringService.isEmpty(didacticComment)) {
                errorMessages.push(strings["invalidDidacticCommentValue"]);
            }
            if (true === _this.stringService.isEmpty(didacticRemark)) {
                errorMessages.push(strings["invalidDidacticRemarkValue"]);
            }
            if (false === _this.integerService.isInteger(privacy)) {
                errorMessages.push(strings["invalidPrivacyValue"]);
            }
            if (false === _this.integerService.isInteger(author)) {
                errorMessages.push(strings["invalidAuthorValue"]);
            }
            if (false === _this.integerService.isInteger(recommendation)) {
                errorMessages.push(strings["invalidRecommendationValue"]);
            }
            if (subjectsCheckboxes.length === 0) {
                errorMessages.push(strings["noSubjectsErrorMessage"]);
            }
            if (categoryCheckboxes.length === 0) {
                errorMessages.push(strings["noCategoriesErrorMessage"]);
            }
            if (tagCheckboxes.length === 0) {
                errorMessages.push(strings["noTagsErrorMessage"]);
            }

            if (errorMessages.length > 0) {
                _this.modal.prompt(
                    strings["modalErrorTitle"]
                    , errorMessages.join("<br>")
                    , null
                    , strings["modalNegative"]
                );
                return;
            }

            let values = {
                "google-store-url": googleStoreUrl,
                "ios-store-url": iosStoreUrl,
                "ios-privacy": _this.integerService.parse(iosPrivacy),
                "usage": _this.floatService.parse(usage),
                "results-quality": _this.floatService.parse(resultsQuality),
                "presentability": _this.floatService.parse(presentability),
                "didactic-comment": _this.stringService.trim(didacticComment),
                "didactic-remark": _this.stringService.trim(didacticRemark),
                "privacy": _this.integerService.parse(privacy),
                "privacy-comment": _this.stringService.trim(privacyComment),
                "subjects": subjectsCheckboxes,
                "categories": categoryCheckboxes,
                "tags": tagCheckboxes,
                "author": _this.integerService.parse(author),
                "recommendation": _this.integerService.parse(recommendation)
            };

            _this.fetcher.post(
                _this.getUrl()
                , values
                , (data, textStatus, jQxhr) => {
                    let message = [];
                    let obj = jQuery.parseJSON(data);
                    let responseCode = obj["response_code"];
                    let title = null;

                    if (RESPONSE_CODE_OK === responseCode) {
                        title = strings["modalTitle"];
                        message.push(strings["appUpdatedMessage"]);
                    } else
                        if (RESPONSE_CODE_NOT_OK === responseCode) {
                            title = strings["modalErrorTitle"];
                            message.push(strings["appNotUpdatedErrorMessage"]);
                        }

                    _this.modal.prompt(
                        title
                        , message.join("<br>")
                        , null
                        , "OK"
                    );

                }
                , (data, textStatus, errorThrown) => {

                    _this.modal.prompt(
                        strings["modalErrorTitle"]
                        , strings["modalTechnicalError"]
                        , null
                        , strings["modalNegative"]
                    );

                }
                , () => {
                    _this.lock();
                }
                , () => {
                    _this.unlock();
                }
            );
            return false;
        });

        const deleteButton = $("#deleteButton");
        deleteButton.on("click", () => {
            _this.modal.prompt(
                strings["deleteAppConfirmTitle"]
                , strings["deleteAppConfirmMessage"]
                , strings["deleteAppConfirmPositive"]
                , strings["deleteAppConfirmNegative"]
                , (button) => {

                    button.on("click", () => {
                            _this.fetcher.delete(
                                _this.routes.getDeleteApp(deleteButton.data("app-id"))
                                , {}
                                , (data, textStatus, jQxhr) => {
                                    let obj = jQuery.parseJSON(data);
                                    let responseCode = obj["response_code"];

                                    if (RESPONSE_CODE_OK === responseCode) {
                                        button.addClass("btn-success");
                                        button.html("App wurde gelöscht");

                                        setTimeout(function () {
                                            let host = $("#data-node").attr("data-host");
                                            window.location.replace(host);
                                        }, 3000);

                                    } else {
                                        button.addClass("btn-warning");
                                        button.html("Fehler beim Löschen der App!");
                                    }


                                }
                                , (jqXhr, textStatus, errorThrown) => {
                                    button.removeClass("btn-primary");
                                    button.addClass("btn-warning");
                                    button.html("Fehler beim Löschen der App!");
                                }
                                , (d) => {
                                    button.attr('disabled', true);
                                }
                            )
                        }
                    )
                }
            );
        })

    }

    listenForURL() {

        const _this = this;
        const iosStoreUrl = $("#ios-store-url");
        const googleStoreUrl = $("#google-store-url");
        const privacyWrapper = $("#ios-privacy-wrapper");

        iosStoreUrl.bind("input", () => {
            let value = iosStoreUrl.val();
            const validUrl = _this.urlService.validURL(value);

            privacyWrapper.hide();
            if (true === validUrl) {
                privacyWrapper.show();
            }

            if (false === validUrl) return

            iosStoreUrl.val(
                _this.urlService.normalize(value)
            );

        });

        googleStoreUrl.bind("input", function () {
            let value = iosStoreUrl.val();
            const validUrl = _this.urlService.validURL(value);

            if (false === validUrl) return

            googleStoreUrl.val(
                _this.urlService.normalize(value)
            );
        });
    }

    lock() {
        if (this.environment.isDebug()) return;
        const submitButton = $("#Submit-button");
        submitButton.attr('disabled', true);
        $("body").addClass('loading');
    }

    unlock() {
        if (this.environment.isDebug()) return;
        const submitButton = $("#Submit-button");
        submitButton.attr('disabled', false);
        $("body").removeClass('loading');
    }

    getUrl() {
        return this.mode === MODE_EDIT
            ? this.routes.getUpdateApp()
            : this.routes.getNewApp();
    }

    determineAppType() {
        let googleStoreUrl = $("#google-store-url").val();
        let iosStoreUrl = $("#ios-store-url").val();

        if (true === this.urlService.validURL(googleStoreUrl)) {
            this.appType = APP_TYPE_ANDROID;
        }

        if (true === this.urlService.validURL(iosStoreUrl)) {

            this.appType = APP_TYPE_IOS;
            if (APP_TYPE_ANDROID === this.appType) {
                this.appType = APP_TYPE_BOTH;
            }

        }

    }

    async getStrings() {
        const strings = await this.stringLoader.read();
        return this.mode === MODE_EDIT
            ? strings['edit_app']
            : strings['new_app']
    }
}
