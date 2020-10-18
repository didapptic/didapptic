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
import 'bootstrap-select';

const TYPE_OPERATING_SYSTEM = "system.operating.type";
const TYPE_SUBJECT = "subject.type";
const TYPE_CATEGORY = "category.type";
const TYPE_TEXT = "text.type";

export class Filter {

    constructor(
        stringService
        , arrayService
        , stringLoader
    ) {
        this.stringService = stringService;
        this.arrayService = arrayService;

        this.filterCache = {
            [TYPE_OPERATING_SYSTEM]: []
            , [TYPE_SUBJECT]: []
            , [TYPE_CATEGORY]: []
            , [TYPE_TEXT]: ""
        };
        this.stringLoader = stringLoader;
    }

    async run() {
        const operatingSystem = $("#os-select-box");
        const subject = $("#subject-select-box");
        const category = $("#category-select-box");
        const _this = this;

        let strings = await this.stringLoader.read();
        strings = strings["main"]["strings"];

        const selectPickerOptions = {
            deselectAllText: strings["filtersDesectAllText"]
            , selectAllText: strings["filtersSectAllText"]
            , liveSearchPlaceholder: strings["filtersSearchPlaceholder"]
            , noneSelectedText: strings["filtersNoneSelected"]
            , noneResultsText: strings["filtersNoneResult"]
            , liveSearch: false
            , liveSearchNormalize: true
            , actionsBox: true
            , size: 4
            , virtualScroll: false
            , header: strings["filtersHeader"]
            , countSelectedText: (numberOfSelectedOptions, totalOptions) => {
                let text = _this.stringService.replace(
                    strings['selectedTextNumber']
                    , '{numberOfSelectedOptions}'
                    , numberOfSelectedOptions
                );
                text = _this.stringService.replace(
                    text
                    , '{totalOptions}'
                    , totalOptions
                )
                return text;
            }
            , selectedTextFormat: 'count > 2'
        };

        operatingSystem.selectpicker(selectPickerOptions);
        subject.selectpicker(selectPickerOptions);
        category.selectpicker(selectPickerOptions);

        await this.registerFilter(
            operatingSystem
            , TYPE_OPERATING_SYSTEM
        );

        await this.registerFilter(
            subject
            , TYPE_SUBJECT
        );

        await this.registerFilter(
            category
            , TYPE_CATEGORY
        );

        await this.registerSearchFilter();
    }

    async registerFilter(element, type) {
        const _this = this;
        element.on(
            'changed.bs.select'
            , (e, clickedIndex, isSelected, previousValue) => {
                _this.filterCache[type] = $(e.currentTarget).val() || [];
                _this.filter();
            });
    }

    async registerSearchFilter() {
        const element = $("#search-filter");
        const _this = this;
        element.on(
            "keyup"
            , () => {
                _this.filterCache[TYPE_TEXT] = element.val() || "";
                _this.filter();
            }
        )
    }

    async filter() {
        const _this = this;
        const element = $('.ab-main');

        $.each(element,
            (index, value) => {
                const currentElement = $(value);
                let category = currentElement.data("category-ids");
                category = this.arrayService.stringify(category);
                let operatingSystem = currentElement.data("operating-system-ids").toString();
                let subject = currentElement.data("subject-ids");
                subject = this.arrayService.stringify(subject);
                let name = currentElement.data("name");

                if (
                    _this.inArray(_this.filterCache[TYPE_OPERATING_SYSTEM], operatingSystem)
                    && _this.hasIntersection(_this.filterCache[TYPE_SUBJECT], subject)
                    && _this.hasIntersection(_this.filterCache[TYPE_CATEGORY], category)
                    && _this.contains(
                    name.toLowerCase()
                    , (_this.filterCache[TYPE_TEXT] || "").toLowerCase()
                    )
                ) {
                    currentElement.show();
                } else {
                    currentElement.hide();
                }
            });
    }

    inArray(values, selected) {
        if (0 === values.length) return true;
        return this.arrayService.inArray(values, selected);
    }

    hasIntersection(selected, values) {
        if (0 === selected.length) return true;
        return this.arrayService.hasIntersection(selected, values);
    }

    contains(value, selected) {
        if (this.stringService.isEmpty(selected)) return true;
        return this.stringService.contains(
            value.toLowerCase()
            , selected.toLowerCase()
        );
    }

}
