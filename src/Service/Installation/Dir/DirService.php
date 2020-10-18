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

namespace Didapptic\Service\Installation\Dir;

use Didapptic\Object\Environment;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class DirService
 *
 * @package Didapptic\Service\Installation\Dir
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class DirService {

    /** @var string */
    private $appRoot;

    /** @var Environment */
    private $environment;

    /**
     * DirService constructor.
     *
     * @param string      $appRoot
     * @param Environment $environment
     */
    public function __construct(
        string $appRoot
        , Environment $environment
    ) {
        $this->appRoot     = $appRoot;
        $this->environment = $environment;
    }

    /**
     * @return ArrayList
     */
    public function getNecessaryDirs(): ArrayList {
        $dirs = new ArrayList();
        // data dir
        $dirs->add("{$this->appRoot}/data/");
        $dirs->add("{$this->appRoot}/data/bkp/");
        $dirs->add("{$this->appRoot}/data/bkp/android/");
        $dirs->add("{$this->appRoot}/data/bkp/ios/");
        $dirs->add("{$this->appRoot}/data/img/");
        $dirs->add("{$this->appRoot}/data/log/");
        $dirs->add("{$this->appRoot}/data/string/");
        $dirs->add("{$this->appRoot}/data/string/frontend");
        $dirs->add("{$this->appRoot}/data/sys/");
        $dirs->add("{$this->appRoot}/data/template/");
        $dirs->add("{$this->appRoot}/data/template/email/");
        $dirs->add("{$this->appRoot}/data/template/frontend/");

        // bin dir
        $dirs->add("{$this->appRoot}/bin/");

        // cron dir
        $dirs->add("{$this->appRoot}/cron/");

        $dirs = $this->addDevDirs($dirs);
        return $dirs;
    }

    /**
     * @param ArrayList $dirs
     *
     * @return ArrayList
     */
    private function addDevDirs(ArrayList $dirs): ArrayList {
        if (false === $this->environment->isDebug()) return $dirs;
        // config dir
        $dirs->add("{$this->appRoot}/config/");
        $dirs->add("{$this->appRoot}/config/phinx/");
        $dirs->add("{$this->appRoot}/config/phpstan/");
        $dirs->add("{$this->appRoot}/config/vagrant/");

        // asset dirs
        $dirs->add("{$this->appRoot}/less/");
        $dirs->add("{$this->appRoot}/js/");
        $dirs->add("{$this->appRoot}/test/");
        $dirs->add("{$this->appRoot}/vendor/");
        return $dirs;
    }

}
