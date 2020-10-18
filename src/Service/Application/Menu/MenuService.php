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

namespace Didapptic\Service\Application\Menu;

use Didapptic\Didapptic;
use Didapptic\Object\Environment;
use Didapptic\Object\Menu;
use Didapptic\Service\HTTP\URL\URLService;
use Didapptic\Service\User\Permission\PermissionService;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\SimpleRBAC\Handler\PermissionHandler;

/**
 * Class MenuService
 *
 * @package Didapptic\Service
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MenuService {

    /** @var Environment */
    private $environment;
    /** @var PermissionHandler */
    private $permissionHandler;
    /** @var PermissionService */
    private $permissionService;
    /** @var URLService */
    private $urlService;

    public function __construct(
        Environment $environment
        , PermissionHandler $permissionHandler
        , PermissionService $permissionService
        , URLService $urlService
    ) {
        $this->environment       = $environment;
        $this->permissionHandler = $permissionHandler;
        $this->permissionService = $permissionService;
        $this->urlService        = $urlService;
    }

    /**
     * @param Menu[] $menuArray
     * @param bool   $loggedIn
     *
     * @return ArrayList|null
     */
    public function prepareMenu(array $menuArray, bool $loggedIn = false): ?ArrayList {
        $list = new ArrayList();

        foreach ($menuArray as $menu) {

            if (false === $this->permissionHandler->hasPermission($this->permissionService->toPermission($menu->getPermissionId()))) {
                continue;
            }

            if (
                $menu->getId() === Menu::ID_LOGIN
                && true === $loggedIn
            ) {
                $menu->setName(
                    "Logout"
                );
                $menu->setHref(
                    "menu/logout/"
                );
            }

            if (false === $this->urlService->isURL($menu->getHref())) {
                $menu->setHref(Didapptic::getBaseURL(true) . "/" . $menu->getHref());
            }

            $list->add($menu);
        }

        return $list;

    }


}
