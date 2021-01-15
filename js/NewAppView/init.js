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
import {URLService} from "../Library/Service/URLService";
import {Environment} from "../Library/Environment";
import {Fetcher} from "../Library/Backend/Fetcher";
import {Routes} from "../Library/App/Form/Routes/Routes";
import {Routes as GlobalRoutes} from "../Library/Backend/Routes/Routes";
import {Parser} from "../Library/Template/Parser";
import {TemplateLoader} from "../Library/Storage/Template/TemplateLoader";
import {StringLoader} from "../Library/Storage/String/StringLoader";
import {DidappticModal} from "../Library/Modal/DidappticModal";
import {NewSubject} from "../Library/App/Subject/NewSubject";
import {NewCategory} from "../Library/App/Category/NewCategory";
import {NewTag} from "../Library/App/Tag/NewTag";
import {AppForm, MODE_NEW} from "../Library/App/Form/AppForm";
import {FloatService} from "../Library/Service/DataType/FloatService";
import {StringService} from "../Library/Service/DataType/StringService";
import {IntegerService} from "../Library/Service/DataType/IntegerService";
import "process";

$(document).ready(async () => {
    const environment = new Environment();
    const fetcher = new Fetcher();
    const floatService = new FloatService();
    const integerService = new IntegerService();
    const stringService = new StringService();
    const routes = new Routes(
        environment
    );
    const globalRoutes = new GlobalRoutes(
        environment
    );
    const templateParser = new Parser();

    const templateLoader = new TemplateLoader(
        fetcher
        , globalRoutes
    );
    await templateLoader.load(true);

    const stringLoader = new StringLoader(
        fetcher
        , globalRoutes
    );
    await stringLoader.load(true);

    const modal = new DidappticModal(
        templateLoader
        , templateParser
    )

    const urlService = new URLService();

    const newSubject = new NewSubject(
        modal
        , stringLoader
        , templateLoader
        , templateParser
        , fetcher
        , routes.getNewSubject()
    );

    const newCategory = new NewCategory(
        modal
        , stringLoader
        , templateLoader
        , templateParser
        , fetcher
        , routes.getNewCategory()
    );
    const newTag = new NewTag(
        modal
        , stringLoader
        , templateLoader
        , templateParser
        , fetcher
        , routes.getNewTag()
    );

    const editApp = new AppForm(
        environment
        , fetcher
        , routes
        , modal
        , urlService
        , stringLoader
        , newSubject
        , newCategory
        , newTag
        , MODE_NEW
        , floatService
        , stringService
        , integerService
    );
    await editApp.run();
});
