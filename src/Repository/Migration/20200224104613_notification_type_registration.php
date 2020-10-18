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

class NotificationTypeRegistration extends AbstractMigration {

    private const NOTIFICATION_REGISTRATION  = 3;
    private const NOTIFICATION_PASSWORD_LOST = 4;
    private const NOTIFICATION_NEW_APP       = 5;

    private const TYPE_ACTIVE             = 1;
    private const TYPE_MAIL_NAME          = "mail.type.notification";
    private const TYPE_MANDATORY          = 1;
    private const TYPE_PASSWORD_LOST_MAIL = 4;
    private const TYPE_NEW_APP            = 5;
    private const TYPE_MAIL               = 1;

    private const SYSTEM_USER_ID = 9999;

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
        $this->table("notification_queue")
            ->addColumn(
                "notification_id"
                , "integer"
                , [
                    "after"     => "delay"
                    , "null"    => false
                    , "default" => 0
                ]
            )
            ->save();

        $this->execute("UPDATE notification_queue SET notification_id = 1 WHERE subject = \"neue Nachricht erhalten | didapptic\"");

        $this->table("notification")
            ->insert(
                [
                    "id"          => self::NOTIFICATION_REGISTRATION
                    , "name"      => "Benachrichtigung über Registrierung"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();


        $this->table("notification_type")
            ->insert(
                [
                    "id"                => 3
                    , "name"            => self::TYPE_MAIL_NAME
                    , "notification_id" => self::NOTIFICATION_REGISTRATION
                    , "mandatory"       => 0
                    , "create_ts"       => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->execute("INSERT INTO notification_type_user (notification_type_id, user_id, active) SELECT " . self::NOTIFICATION_REGISTRATION . ", id, " . self::TYPE_MAIL . " FROM user where id != " . self::SYSTEM_USER_ID);

        $this->table("notification")
            ->insert([
                    "id"          => self::NOTIFICATION_PASSWORD_LOST
                    , "name"      => "Passwort vergessen"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->table("notification_type")
            ->insert(
                [
                    "id"                => self::TYPE_PASSWORD_LOST_MAIL
                    , "name"            => self::TYPE_MAIL_NAME
                    , "mandatory"       => self::TYPE_MANDATORY
                    , "notification_id" => self::NOTIFICATION_PASSWORD_LOST
                    , "create_ts"       => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->execute("INSERT INTO
                                    notification_type_user (
                                            notification_type_id
                                            , user_id
                                            , active
                                    ) SELECT
                                            " . self::TYPE_PASSWORD_LOST_MAIL . "
                                            , id
                                            , " . self::TYPE_ACTIVE . "
                                      FROM user
                                        where id != " . self::SYSTEM_USER_ID
        );

        $this->table("notification")
            ->insert([
                    "id"          => self::NOTIFICATION_NEW_APP
                    , "name"      => "Neue App hinzugefügt"
                    , "create_ts" => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->table("notification_type")
            ->insert(
                [
                    "id"                => self::TYPE_NEW_APP
                    , "name"            => self::TYPE_MAIL_NAME
                    , "mandatory"       => self::TYPE_MANDATORY
                    , "notification_id" => self::NOTIFICATION_NEW_APP
                    , "create_ts"       => DateTimeUtil::getUnixTimestamp()
                ]
            )
            ->save();

        $this->execute("INSERT INTO
                                    notification_type_user (
                                            notification_type_id
                                            , user_id
                                            , active
                                    ) SELECT
                                            " . self::TYPE_NEW_APP . "
                                            , id
                                            , " . self::TYPE_ACTIVE . "
                                      FROM user
                                        where id != " . self::SYSTEM_USER_ID
        );

    }

}
