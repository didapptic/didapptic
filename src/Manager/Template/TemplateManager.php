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

namespace Didapptic\Manager\Template;

use Didapptic\Object\Constant\Extension;
use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap as HashTable;
use doganoo\PHPAlgorithms\Datastructure\Sets\HashSet;
use RecursiveDirectoryIterator;
use SplFileInfo;
use Twig\Environment as TwigEnv;
use Twig\Loader\FilesystemLoader;

/**
 * Class TemplateManager
 *
 * @package Didapptic\Manager\Template
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class TemplateManager {

    /** @var FilesystemLoader */
    private $loader;
    /** @var TwigEnv */
    private $env;
    /** @var HashTable */
    private $map;
    /** @var HashSet */
    private $names;

    public function __construct() {
        $this->names  = new HashSet();
        $this->map    = new HashTable();
        $this->loader = new FilesystemLoader();
        $this->env    = new TwigEnv($this->loader);
    }

    public function addAll(array $paths): void {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    public function addPath(string $path): void {
        $this->loader->addPath($path);

        $iterator = new RecursiveDirectoryIterator($path);
        /** @var SplFileInfo $info */
        foreach ($iterator as $info) {
            if (
                true === $info->isFile() &&
                Extension::TWIG === $info->getExtension()
            ) {
                $this->names->add($info->getRealPath());
            }
        }
    }

    public function replace(string $name, array $value): void {
        if ($this->map->containsKey($name)) {
            $arr = $this->map->get($name);
            $arr = array_merge($arr, $value);
            $this->map->put($name, $arr);
        } else {
            $this->map->put($name, $value);
        }
    }

    public function getAllRaw(): HashTable {
        $table = new HashTable();
        foreach ($this->names->toArray() as $name) {
            $baseName = basename($name);

            $template = $this->getRawTemplate(
                $baseName
            );
            $table->put($name, $template);
        }
        return $table;
    }

    public function getRawTemplate(string $name): string {
        return $this->env->getLoader()->getSourceContext($name)->getCode();
    }

    public function render(string $name): string {
        $variables = [];
        if ($this->map->containsKey($name)) {
            $variables = $this->map->get($name);
        }
        $rendered = $this->env->render($name, $variables);
        return $rendered;
    }

    protected function getEnvironment(): TwigEnv {
        return $this->env;
    }

}
