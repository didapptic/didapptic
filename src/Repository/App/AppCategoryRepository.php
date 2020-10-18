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

namespace Didapptic\Repository\App;

use Didapptic\Object\App;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

class AppCategoryRepository {

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
        $this->connector->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function addCategory(int $appId, int $categoryId): bool {
        $sql       = "insert into `app_category` (`app_id`, `category_id`) VALUES (:app_id, :cat_id);";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;

        $statement->bindParam(":app_id", $appId);
        $statement->bindParam(":cat_id", $categoryId);
        return $statement->execute();
    }

    public function deleteCategoriesByApp(App $app): bool {
        $this->connector->startTransaction();
        $sql       = "DELETE FROM `app_category` WHERE `app_id` = :app_id;";
        $statement = $this->connector->prepare($sql);
        $appId     = $app->getId();
        $statement->bindParam("app_id", $appId);
        $executed = $statement->execute();
        if ($executed) {
            $this->connector->commit();
            return true;
        } else {
            $this->connector->rollback();
            return false;
        }
    }
}
