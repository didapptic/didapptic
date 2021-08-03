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

namespace Didapptic\Repository;

use doganoo\PHPUtil\Storage\PDOConnector;
use Exception;
use PDO;

/**
 * Class CategoryManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class CategoryRepository {

    /** @var PDOConnector */
    private $connector;

    /**
     * CategoryRepository constructor.
     *
     * @param PDOConnector $connector
     */
    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    /**
     * @return array
     */
    public function getCategories(): array {
        $array = [];
        $sql   = "SELECT `id`, `category` FROM `category` ORDER BY `category` ASC;";

        try {
            $statement = $this->connector->prepare($sql);
        } catch (Exception $exception) {
            // TODO log!
            return $array;
        }

        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id   = $row[0];
            $name = $row[1];
            if ($id === "" || $name === "") {
                continue;
            }
            $array[$id] = $name;
        }
        return $array;
    }

    /**
     * @param int $appId
     *
     * @return array
     */
    public function getCategoriesByAppId(int $appId): array {
        $array = [];
        $sql   = "  
                        SELECT 
                            c.`id`
                          , c.`category` 
                        FROM `category` c 
                            LEFT JOIN `app_category` ac ON c.`id` = `ac`.`category_id`
                            LEFT JOIN `app` a on ac.`app_id` = a.`id`
                        WHERE a.`id` = :app_id
                        ORDER BY `category` ASC;";
        try {
            $statement = $this->connector->prepare($sql);
        } catch (Exception $exception) {
            return $array;
        }
        $statement->bindParam("app_id", $appId);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id   = $row[0];
            $name = $row[1];
            if ($id === "" || $name === "") {
                continue;
            }
            $array[$id] = $name;
        }
        return $array;
    }

    /**
     * @param string $categoryId
     *
     * @return bool
     */
    public function exists(string $categoryId): bool {
        $sql = "SELECT EXISTS(
                            SELECT `id` 
                            FROM `category`
                            WHERE `id`= :category_id
                        )";
        try {
            $statement = $this->connector->prepare($sql);
        } catch (Exception $exception) {
            return false;
        }
        $statement->bindParam(":category_id", $categoryId);
        $statement->execute();
        $row    = $statement->fetch(PDO::FETCH_BOTH);
        $exists = (bool) $row[0];
        return true === $exists;
    }

    /**
     * @param string $category
     *
     * @return int|null
     */
    public function insert(string $category): ?int {
        $sql = "INSERT INTO `category` (
                            `category`
                        ) VALUES (
                            :category
                        );";

        try {
            $statement = $this->connector->prepare($sql);
        } catch (Exception $exception) {
            return null;
        }

        $statement->bindParam(":category", $category);

        $executed = $statement->execute();
        if (false === $executed) return null;

        $lastInsertId = $this->connector->getLastInsertId();

        return (int) $lastInsertId;
    }

}
