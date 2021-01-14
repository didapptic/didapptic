<?php
declare(strict_types=1);
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

namespace Didapptic\Submit;

use Didapptic\Repository\CategoryRepository;

/**
 * Class AddCategorySubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AddCategorySubmit extends AbstractSubmit {

    /** @var string */
    private $category;

    /** @var CategoryRepository */
    private $categoryManager;

    public function __construct(CategoryRepository $categoryManager) {
        parent::__construct();
        $this->categoryManager = $categoryManager;
    }

    protected function valid(): bool {
        $category = $this->getArgument("value");
        if (null === $category || "" === trim($category)) return false;
        $this->category = $category;
        return true;
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {
        $id      = $this->categoryManager->insert($this->category);
        $success = null !== $id && $id > 0;
        $id      = null;

        $this->addResponse("id", $id);

        return $success;
    }

    protected function onDestroy(): void {

    }

}
