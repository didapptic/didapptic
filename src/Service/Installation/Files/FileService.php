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

namespace Didapptic\Service\Installation\Files;

use Didapptic\Object\Environment;
use Didapptic\Object\File;
use Didapptic\Object\Material;
use Didapptic\Repository\FileRepository;
use Didapptic\Repository\MaterialRepository;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class FileService
 *
 * @package Didapptic\Service\Installation\Files
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class FileService {

    /** @var string */
    private $appRoot;
    /** @var FileRepository */
    private $fileManager;
    /** @var MaterialRepository */
    private $materialManager;
    /** @var Environment */
    private $environment;

    public function __construct(
        string $appRoot
        , FileRepository $fileManager
        , MaterialRepository $materialManager
        , Environment $environment
    ) {
        $this->appRoot         = $appRoot;
        $this->fileManager     = $fileManager;
        $this->materialManager = $materialManager;
        $this->environment     = $environment;
    }

    /**
     * returns a list of all files that are necessary to run the app
     *
     * @return ArrayList
     */
    public function getNecessaryFiles(): ArrayList {
        $files = new ArrayList();

        // cron dir
        $files->add("{$this->appRoot}/cron/cron.php");

        // data dir
        $files->add("{$this->appRoot}/data/log/sys.log");
        $files->add("{$this->appRoot}/data/sys/sys.properties");

        $files = $this->addDevFiles($files);

        return $files;
    }

    private function addDevFiles(ArrayList $files): ArrayList {
        if (false === $this->environment->isDev()) return $files;

        // the following files are only needed
        // for development. When deploying on
        // any stage, we do not need these files
        // after installing dependencies

        // config dir
        $files->add("{$this->appRoot}/config/phinx/phinx.php");
        $files->add("{$this->appRoot}/config/phpstan/bootstrap.php");
        $files->add("{$this->appRoot}/config/phpstan/phpstan.neon");
        $files->add("{$this->appRoot}/config/vagrant/bootstrap.sh");

        // root dir
        $files->add("{$this->appRoot}/composer.json");
        $files->add("{$this->appRoot}/composer.lock");
        $files->add("{$this->appRoot}/package.json");
        $files->add("{$this->appRoot}/package-lock.json");
        $files->add("{$this->appRoot}/webpack.config.js");
        $files->add("{$this->appRoot}/VAGRANTFILE");

        // data dir
        $files->add("{$this->appRoot}/data/sys/sys.properties-sample");

        // log dir
        $files->add("{$this->appRoot}/data/log/sys.log");

        // test dir
        $files->add("{$this->appRoot}/test/phpunit.xml");


        return $files;
    }

    public function remove(File $file, Material $material): bool {
        $deleted = $this->materialManager->disconnectFromFile($material, $file);
        if (false === $deleted) return false;
        $deleted = $this->fileManager->delete($file->getId());
        if (false === $deleted) return false;
        $deleted = @unlink($file->getPath());
        if (false === $deleted) return false;
        return true;
    }


}
