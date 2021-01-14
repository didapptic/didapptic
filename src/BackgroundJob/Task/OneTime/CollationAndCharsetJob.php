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

namespace Didapptic\BackgroundJob\Task\OneTime;

use Didapptic\Object\Environment;
use Didapptic\Repository\DatabaseRepository;
use doganoo\Backgrounder\Task\Task;
use function count;

/**
 * Class CollationAndCharsetJob
 *
 * @package Didapptic\BackgroundJob\Task\OneTime
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class CollationAndCharsetJob extends Task {

    public const DEFAULT_COLLATION     = "utf8mb4_unicode_ci";
    public const DEFAULT_CHARACTER_SET = "utf8mb4";

    /** @var Environment */
    private $environment;
    /** @var DatabaseRepository */
    private $databaseRepository;
    /** @var array */
    private $schema;
    /** @var array */
    private $tables;

    /**
     * CollationAndCharsetJob constructor.
     *
     * @param Environment        $properties
     * @param DatabaseRepository $databaseManager
     */
    public function __construct(
        Environment $properties
        , DatabaseRepository $databaseManager
    ) {
        $this->environment        = $properties;
        $this->databaseRepository = $databaseManager;
        $this->schema             = [];
        $this->tables             = [];
    }

    protected function preRun(): void {
        /** @phpstan-ignore-next-line */
        $dbInfo = $this->environment->getDatabaseInfo();
        $name   = $dbInfo["db_name"];
        $schema = $this->databaseRepository->getSchemaInformation($name);
        $tables = $this->databaseRepository->getTableInformation($name);

        if ($schema["collation"] === CollationAndCharsetJob::DEFAULT_COLLATION) unset($schema["collation"]);
        if ($schema["character_set"] === CollationAndCharsetJob::DEFAULT_CHARACTER_SET) unset($schema["character_set"]);
        $this->schema = $schema;


        foreach ($tables as $key => $table) {
            if ($table["collation"] === CollationAndCharsetJob::DEFAULT_COLLATION) unset($table["collation"]);
            if ($table["character_set"] === CollationAndCharsetJob::DEFAULT_CHARACTER_SET) unset($table["character_set"]);
            if (count($table) === 0) unset($tables[$key]);
        }
        $this->tables = $tables;
    }

    protected function onAction(): void {

    }

    protected function action(): bool {
        /** @phpstan-ignore-next-line */
        $dbInfo = $this->environment->getDatabaseInfo();
        $name   = $dbInfo["db_name"];

        if (0 < count($this->schema)) {
            $this->databaseRepository->updateSchemaInformation(
                $name
                , CollationAndCharsetJob::DEFAULT_CHARACTER_SET
                , CollationAndCharsetJob::DEFAULT_COLLATION);
        }

        if (0 < count($this->tables)) {
            foreach ($this->tables as $key => $table) {
                $this->databaseRepository->updateTableInformation($key, CollationAndCharsetJob::DEFAULT_CHARACTER_SET, CollationAndCharsetJob::DEFAULT_COLLATION);
            }
        }

        $tables = $this->databaseRepository->getTableInformation($name);
        foreach ($tables as $key => $value) {
            $this->databaseRepository->updateColumns($key);
        }

        return true;
    }

    protected function onClose(): void {

    }

}
