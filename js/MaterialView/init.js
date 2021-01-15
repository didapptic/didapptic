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
import 'jquery-ui'
import 'jquery-ui-dist/jquery-ui'
import 'jquery-form/dist/jquery.form.min'
import {Fetcher} from "../Library/Backend/Fetcher";
import {Material} from "./Material";
import {Routes} from "./Routes/Routes";
import {StringLoader} from "../Library/Storage/String/StringLoader";
import {DidappticModal} from "../Library/Modal/DidappticModal";
import {Parser} from "../Library/Template/Parser";
import {Routes as GlobalRoutes} from "../Library/Backend/Routes/Routes";
import {TemplateLoader} from "../Library/Storage/Template/TemplateLoader";
import {Environment} from "../Library/Environment";
import "process";

$(document).ready(async () => {
    const fetcher = new Fetcher();
    const environment = new Environment();
    const routes = new Routes(
        environment
    );
    const templateParser = new Parser();
    const globalRoutes = new GlobalRoutes(
        environment
    );
    const stringLoader = new StringLoader(
        fetcher
        , globalRoutes
    );
    await stringLoader.load(true);

    const templateLoader = new TemplateLoader(
        fetcher
        , globalRoutes
    );
    await templateLoader.load(true);

    const modal = new DidappticModal(
        templateLoader
        , templateParser
    )

    const material = new Material(
        fetcher
        , routes
        , stringLoader
        , modal
        , templateParser
        , templateLoader
    );
    await material.run();

});
