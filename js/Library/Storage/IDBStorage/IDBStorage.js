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
import localforage from "localforage";

export class IDBStorage {

    constructor(dbName) {
        this.store = localforage.createInstance({
            name: dbName
            , version: 1
            , storeName: dbName
        });
    }

    async add(key, value) {
        await this.store.setItem(key, value)
    }

    async clear() {
        await this.store.clear()
    }

    async getAll() {
        const result = {};

        const keys = await this.store.keys();

        for (let key in keys) {
            if (keys.hasOwnProperty(key)) {
                let name = keys[key];
                result[name] = await this.store.getItem(name);
            }
        }

        return result;
    }

    async addAll(values) {
        for (let name in values) {
            if (values.hasOwnProperty(name)) {

                await this.add(
                    name
                    , values[name]
                );

            }
        }

    }

}
