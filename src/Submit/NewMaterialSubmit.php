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

use Didapptic\Didapptic;
use Didapptic\Object\Environment;
use Didapptic\Service\Material\MaterialService;
use Didapptic\Service\User\UserService;
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\SimpleRBAC\Common\IUser;
use Exception;
use function is_uploaded_file;

/**
 * Class NewMaterialSubmit
 *
 * @package Didapptic\Submit
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class NewMaterialSubmit extends AbstractSubmit {

    /** @var array */
    private $parameters;
    /** @var IUser|null */
    private $user;
    /** @var array|null */
    private $files;
    /** @var Environment */
    private $environment;
    /** @var MaterialService */
    private $materialService;
    /** @var UserService */
    private $userService;

    public function __construct(
        Environment $environment
        , UserService $userService
        , MaterialService $materialService
    ) {
        parent::__construct();
        $this->environment     = $environment;
        $this->materialService = $materialService;
        $this->userService     = $userService;
    }

    protected function valid(): bool {
        $this->parameters = $this->getArguments();
        $this->user       = $this->userService->getUser();
        $this->files      = $this->getFiles($this->parameters["files"]["dd__material__input__file__name"]);

        $validDateTime       = $this->validDatetime($this->parameters["dd__date__input"], "d.m.Y");
        $descriptionNotEmpty = $this->parameters["dd__material__description"] !== "";
        $nameNotEmpty        = $this->parameters["dd__name__input"] !== "";
        $enoughFiles         = count($this->parameters["files"]) > 0;
        $userExists          = null !== $this->user;
        $enoughFiles2        = count((array) $this->files) > 0;

        return
            true === $validDateTime
            && true === $descriptionNotEmpty
            && true === $nameNotEmpty
            && true === $enoughFiles
            && true === $userExists
            && true === $enoughFiles2;

    }

    private function getFiles(array $files): ?array {

        $result   = [];
        $names    = $files['name'] ?? null;
        $tmpNames = $files['tmp_name'] ?? null;
        $sizes    = $files['size'] ?? null;
        $errors   = $files['error'] ?? null;
        $types    = $files['type'] ?? null;

        if (null === $names) return null;
        if (null === $tmpNames) return null;
        if (null === $sizes) return null;
        if (null === $errors) return null;
        if (null === $types) return null;

        $nameCount = count($names);

        for ($i = 0; $i < $nameCount; $i++) {

            $name = $names[$i];
            if (false === is_uploaded_file($tmpNames[$i])) {
                FileLogger::debug("$name is not a uploaded file!");
                continue;
            }

            $dir = Didapptic::getAppRoot() . "/tmp";
            if (false === is_dir($dir)) {
                mkdir($dir);
            }
            $targetName = $dir . $name;
            $moved      = move_uploaded_file($tmpNames[$i], $targetName);

            if (false === $moved || false === is_file($targetName)) {
                FileLogger::debug("could not move " . $tmpNames[$i] . ". Skipping");
                continue;
            }

            if (0 !== (int) $errors[$i]) {
                FileLogger::debug("$name file has errors");
                continue;
            }
            if (0 === (int) $sizes[$i]) {
                FileLogger::debug("$name size equals to 0");
                continue;
            }

            $file     = [
                "name"        => $name
                , "mime_type" => $types[$i]
                , "file_path" => $targetName
            ];
            $result[] = $file;
        }

        return $result;

    }

    private function validDatetime(string $date, string $format): bool {
        return date($format, (int) strtotime($date)) === $date;
    }

    protected function onCreate(): void {

    }

    protected function create(): bool {
        if (null === $this->user) {
            throw new Exception('no user');
        }
        return $this->materialService->upload(
            [
                "date"          => $this->parameters["dd__date__input"]
                , "description" => $this->parameters["dd__material__description"]
                , "name"        => $this->parameters["dd__name__input"]
                , "files"       => $this->files
                , "user_id"     => $this->user->getId()
                , "password"    => $this->parameters["dd__password__input"]
            ]
        );
    }

    protected function onDestroy(): void {
    }

}
