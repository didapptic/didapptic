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

use DateTime;
use Didapptic\Object\Menu;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

/**
 * Class MenuManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class MenuRepository {

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function getMenu(): array {
        $array     = [];
        $sql       = "SELECT 
                        m.`id`
                        , m.`name`
                        , m.`href`
                        , m.`permission_id`
                        , m.`order`
                        , m.`visible`
                        , m.`create_ts` 
                        FROM `menu` m 
                WHERE m.`visible` = :visible 
                ORDER BY m.`order` ASC;";
        $statement = $this->connector->prepare($sql);
        $visible   = 1;
        $statement->bindParam(":visible", $visible);

        $statement->execute();
        if ($statement->rowCount() === 0) {
            return $array;
        }
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $id           = $row[0];
            $name         = $row[1];
            $href         = $row[2];
            $permissionId = $row[3];
            $order        = $row[4];
            $visible      = $row[5];
            $createTs     = $row[6];

            $menu = new Menu();
            $menu->setId((int) $id);
            $menu->setName($name);
            $menu->setHref($href);
            $menu->setPermissionId((int) $permissionId);
            $menu->setOrder((int) $order);
            $menu->setVisible("1" === $visible);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $createTs);
            $menu->setCreateTs($dateTime);
            $array[$id] = $menu;
        }
        return $array;
    }

    public function updateMenu(Menu $menu): bool {
        $sql = "
                update `menu`
                    set `name`          = :name
                      , `href`          = :href
                      , `order`         = :order
                      , `visible`       = :visible
                      , `permission_id` = :permission_id
                    where `id` = :id;
        ";

        $statement = $this->connector->prepare($sql);

        $name         = $menu->getName();
        $href         = $menu->getHref();
        $order        = $menu->getOrder();
        $visible      = true === $menu->isVisible()
            ? 1
            : 0;
        $permissionId = $menu->getPermissionId();
        $id           = $menu->getId();

        $statement->bindParam(":name", $name);
        $statement->bindParam(":href", $href);
        $statement->bindParam(":order", $order);
        $statement->bindParam(":visible", $visible);
        $statement->bindParam(":permission_id", $permissionId);
        $statement->bindParam(":id", $id);

        $statement->execute();

        return $statement->rowCount() > 0;

    }

}
