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

use Didapptic\Object\Permission;
use Didapptic\Object\Role;
use doganoo\PHPUtil\Util\DateTimeUtil;
use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

/**
 * Class NotificationPermission
 *
 * @author Dogan Ucar <dogan@dogan-ucar.de>
 */
class NotificationPermission extends AbstractMigration {

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

        $this->table("notification_type")
            ->addColumn(
                "permission"
                , MysqlAdapter::PHINX_TYPE_INTEGER
                , [
                    "null"      => false
                    , "comment" => "The permission"
                    , "after"   => "mandatory"
                    , "default" => Permission::DEFAULT_PERMISSION
                ]
            )
            ->addForeignKey(
                "permission"
                , 'permission'
                , 'id'
                , [
                "delete"   => 'CASCADE'
                , 'update' => 'CASCADE'
            ])
            ->save();

        $this->table("permission")
            ->insert(
                [
                    "id"          => Permission::NOTIFICATION_REMOVED_APP_MAIL
                    , "name"      => "NOTIFICATION_REMOVED_APP_MAIL"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => Permission::NOTIFICATION_CONTACT_MAIL
                    , "name"      => "NOTIFICATION_CONTACT_MAIL"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => Permission::NOTIFICATION_REGISTER_MAIL
                    , "name"      => "NOTIFICATION_REGISTER_MAIL"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => Permission::NOTIFICATION_PASSWORD_MAIL
                    , "name"      => "NOTIFICATION_REGISTER_MAIL"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => Permission::NOTIFICATION_NEW_APP_MAIL
                    , "name"      => "NOTIFICATION_REGISTER_MAIL"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();
        $this->table("permission_role")
            ->insert(
                [
                    "permission_id" => Permission::NOTIFICATION_REMOVED_APP_MAIL
                    , "role_id"     => Role::ADMIN_ROLE
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => Permission::NOTIFICATION_CONTACT_MAIL
                    , "role_id"     => Role::ADMIN_ROLE
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => Permission::NOTIFICATION_REGISTER_MAIL
                    , "role_id"     => Role::BASIC_ROLE
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => Permission::NOTIFICATION_PASSWORD_MAIL
                    , "role_id"     => Role::BASIC_ROLE
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => Permission::NOTIFICATION_NEW_APP_MAIL
                    , "role_id"     => Role::ADMIN_ROLE
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

    }

}
