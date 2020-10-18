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

/**
 * Class DatabaseManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class DatabaseRepository {

    private $connector = null;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function updateSchemaInformation(string $schemaName, string $characterSet, string $collation): bool {
        $sql       = "ALTER DATABASE $schemaName CHARACTER SET $characterSet COLLATE $collation;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        return $statement->execute();
    }

    public function updateTableInformation(string $table, string $characterSet, string $collation): bool {
        $sql       = "ALTER TABLE $table CONVERT TO CHARACTER SET $characterSet COLLATE $collation;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        return $statement->execute();
    }

    public function getSchemaInformation(string $schemaName): array {
        $result = [];
        $sql    = "SELECT
                default_character_set_name as 'charset'
                , DEFAULT_COLLATION_NAME as 'collation'
             FROM information_schema.schemata s
                WHERE s.schema_name = :name;";

        $statement = $this->connector->prepare($sql);
        if (null === $statement) return $result;
        $statement->bindParam(":name", $schemaName);
        $statement->execute();

        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $characterSet = $row[0];
            $collation    = $row[1];

            $result["character_set"] = $characterSet;
            $result["collation"]     = $collation;
        }
        return $result;
    }

    public function getTableInformation(string $schemaName): array {
        $result    = [];
        $sql       = "SELECT
                    c.character_set_name
                    , c.collation_name
                    , t.table_name
                FROM information_schema.TABLES t,
                     information_schema.COLLATION_CHARACTER_SET_APPLICABILITY c
                WHERE c.collation_name = t.table_collation
                  AND t.table_schema = :schema_name";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return $result;

        $statement->bindParam(":schema_name", $schemaName);
        $statement->execute();

        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $characterSet = $row[0];
            $collation    = $row[1];
            $tableName    = $row[2];

            $result[$tableName] =
                [
                    "character_set" => $characterSet
                    , "collation"   => $collation
                    ,
                ];
        }
        return $result;
    }

    public function updateColumns(string $table) {
        $columns = $this->getColumnNames($table);

        foreach ($columns as $column => $type) {
            if (\strpos($type, "varchar") ||
                \strpos($type, "text")) {

                $sql = 'UPDATE  ' . $table . ' SET
              `' . $column . '`= REPLACE(`' . $column . '`, "ÃŸ", "ß"),
              `' . $column . '`= REPLACE(`' . $column . '`, "Ã¤", "ä"),
              `' . $column . '`= REPLACE(`' . $column . '`, "Ã¼", "ü"),
              `' . $column . '`= REPLACE(`' . $column . '`, "Ã¶", "ö"),
              `' . $column . '`= REPLACE(`' . $column . '`, "Ã„", "Ä"),
              `' . $column . '`= REPLACE(`' . $column . '`, "Ãœ", "Ü"),
              `' . $column . '`= REPLACE(`' . $column . '`, "Ã–", "Ö"),
              `' . $column . '`= REPLACE(`' . $column . '`, "â‚¬","€")';

                $statement = $this->connector->prepare($sql);
                if (null === $statement) continue;
                $statement->execute();
            }
        }
    }

    public function getColumnNames(string $table) {
        $result    = [];
        $sql       = "DESCRIBE $table;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return $result;
        $statement->execute();
        $rows = [];
        while ($row = $statement->fetch(\PDO::FETCH_BOTH)) {
            $rows[$row['Field']] = $row['Type'];
        }
        return $rows;
    }

}
