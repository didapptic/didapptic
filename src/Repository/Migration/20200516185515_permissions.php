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

/**
 * Class Permissions
 *
 * @author Dogan Ucar <dogan@dogan-ucar.de>
 */
class Permissions extends AbstractMigration {

    private const PERMISSION_NEW_SUBJECT              = 28;
    private const PERMISSION_NEW_CATEGORY             = 29;
    private const PERMISSION_NEW_TAG                  = 30;
    private const PERMISSION_MENU_SETTINGS            = 31;
    private const PERMISSION_MENU_PROFILE             = 32;
    private const PERMISSION_MENU_SETTINGS_ADMIN      = 33;
    private const PERMISSION_MENU_SETTINGS_EDIT_USERS = 39;

    private const ROLE_BASIC = 2;
    private const ROLE_ADMIN = 3;

    private const PERMISSION_NEW_SUBJECT_NAME              = "PERMISSION_NEW_SUBJECT";
    private const PERMISSION_NEW_CATEGORY_NAME             = "PERMISSION_NEW_CATEGORY";
    private const PERMISSION_NEW_TAG_NAME                  = "PERMISSION_NEW_TAG";
    private const PERMISSION_MENU_SETTINGS_NAME            = "PERMISSION_MENU_SETTINGS";
    private const PERMISSION_MENU_PROFILE_NAME             = "PERMISSION_MENU_PROFILE";
    private const PERMISSION_MENU_SETTINGS_ADMIN_NAME      = "PERMISSION_MENU_SETTINGS_ADMIN";
    private const PERMISSION_MENU_SETTINGS_EDIT_USERS_NAME = "PERMISSION_MENU_SETTINGS_EDIT_USERS";

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
                    "id"          => self::PERMISSION_NEW_SUBJECT
                    , "name"      => self::PERMISSION_NEW_SUBJECT_NAME
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => self::PERMISSION_NEW_CATEGORY
                    , "name"      => self::PERMISSION_NEW_CATEGORY_NAME
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => self::PERMISSION_NEW_TAG
                    , "name"      => self::PERMISSION_NEW_TAG_NAME
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => self::PERMISSION_MENU_SETTINGS
                    , "name"      => self::PERMISSION_MENU_SETTINGS_NAME
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => self::PERMISSION_MENU_PROFILE
                    , "name"      => self::PERMISSION_MENU_PROFILE_NAME
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => self::PERMISSION_MENU_SETTINGS_ADMIN
                    , "name"      => self::PERMISSION_MENU_SETTINGS_ADMIN_NAME
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "id"          => self::PERMISSION_MENU_SETTINGS_EDIT_USERS
                    , "name"      => self::PERMISSION_MENU_SETTINGS_EDIT_USERS_NAME
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->table("permission_role")
            ->insert(
                [
                    "permission_id" => self::PERMISSION_NEW_SUBJECT
                    , "role_id"     => self::ROLE_BASIC
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => self::PERMISSION_NEW_CATEGORY
                    , "role_id"     => self::ROLE_BASIC
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => self::PERMISSION_NEW_TAG
                    , "role_id"     => self::ROLE_BASIC
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => self::PERMISSION_MENU_SETTINGS
                    , "role_id"     => self::ROLE_BASIC
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => self::PERMISSION_MENU_PROFILE
                    , "role_id"     => self::ROLE_BASIC
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => self::PERMISSION_MENU_SETTINGS_ADMIN
                    , "role_id"     => self::ROLE_ADMIN
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->insert(
                [
                    "permission_id" => self::PERMISSION_MENU_SETTINGS_EDIT_USERS
                    , "role_id"     => self::ROLE_ADMIN
                    , "create_ts"   => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();
    }

}
