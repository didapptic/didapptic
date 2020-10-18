<?php
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

use doganoo\PHPUtil\Util\DateTimeUtil;
use Phinx\Migration\AbstractMigration;

class UserManagementViewPermission extends AbstractMigration {

    private const USER_MANAGEMENT_VIEW_PERMISSION_ID = 25;
    private const BASIC_ROLE                         = 2;

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change() {

        $this->table("permission")
            ->insert(
                [
                    "id"          => self::USER_MANAGEMENT_VIEW_PERMISSION_ID
                    , "name"      => "USER_MANAGEMENT_VIEW"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->table("permission_role")
            ->insert(
                [
                    "permission_id" => self::USER_MANAGEMENT_VIEW_PERMISSION_ID
                    , "role_id"     => self::BASIC_ROLE
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->execute(
            "update menu set name = 'Benutzerverwaltung', href = 'menu/user-management/', permission_id = 25 where id = 10; "
        );


    }

}
