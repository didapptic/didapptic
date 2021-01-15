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
import {MainView} from "./MainView";
import {TemplateLoader} from "../Library/Storage/Template/TemplateLoader";
import {Fetcher} from "../Library/Backend/Fetcher";
import {Routes as GlobalRoutes} from "../Library/Backend/Routes/Routes";
import {Parser} from "../Library/Template/Parser";
import {StringLoader} from "../Library/Storage/String/StringLoader";
import {Routes} from "./Routes/Routes";
import {Filter} from "./Filter/Filter";
import {Environment} from "../Library/Environment";
import {StringService} from "../Library/Service/DataType/StringService";
import {ArrayService} from "../Library/Service/DataType/ArrayService";
import {IntegerService} from "../Library/Service/DataType/IntegerService";
import {DidappticModal} from "../Library/Modal/DidappticModal";
import "process";

$(document).ready(
    async () => {
        const fetcher = new Fetcher();
        const environment = new Environment();
        const stringService = new StringService();
        const arrayService = new ArrayService();
        const integerService = new IntegerService();

        const globalRoutes = new GlobalRoutes(
            environment
        );
        const routes = new Routes(
            environment
        );
        const templateLoader = new TemplateLoader(
            fetcher
            , globalRoutes
        );

        const stringLoader = new StringLoader(
            fetcher
            , globalRoutes
        );

        await templateLoader.load(true);
        await stringLoader.load(true);

        const templateParser = new Parser();
        const filter = new Filter(
            stringService
            , arrayService
            , stringLoader
        );

        const modal = new DidappticModal(
            templateLoader
            , templateParser
        )

        const mainView = new MainView(
            templateLoader
            , stringLoader
            , templateParser
            , fetcher
            , routes
            , filter
            , integerService
            , modal
        );
        await mainView.run();

    });
