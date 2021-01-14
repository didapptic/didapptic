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

namespace Didapptic\Service\Template;

use Didapptic\Didapptic;
use Didapptic\Object\Environment;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twig\Loader\FilesystemLoader;

/**
 * Class TemplateRenderer
 *
 * @package Didapptic\Service
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class TemplateRenderer {

    public const APP_DELETED = "app_deleted";
    /** @var array */
    private $templates;
    /** @var bool */
    private $isDev;

    public function __construct(Environment $environment) {
        $this->templates = [];
        $this->isDev     = $environment->isDebug();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(Didapptic::getServer()->getTemplatePath())
            , RecursiveIteratorIterator::SELF_FIRST
        );

        /** @var \SplFileInfo $path */
        foreach ($iterator as $path) {
            if ($path->isDir()) {
                $this->templates[] = $path->getRealPath();
            }
        }
    }

    public function loadEmailTemplate(array $context): string {
        return $this->loadTemplate(TemplateRenderer::APP_DELETED, $context);
    }

    public function loadTemplate(string $name, array $context): string {
        $loader = new FilesystemLoader($this->templates);
        $twig   = new \Twig\Environment($loader, []);
        $name   = "$name.html";
        return $twig->load($name)->render($context);
    }

}
