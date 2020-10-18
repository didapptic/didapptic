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

use Phinx\Migration\AbstractMigration;

class NotificationContact extends AbstractMigration {

    private const NOTIFICATION_CONTACT      = 2;
    private const NOTIFICATION_CONTACT_DESC = "Kontaktaufnahme über die Plattform";
    private const TYPE_MAIL                 = 2;
    private const TYPE_MAIL_NAME            = "mail.type.notification";
    private const SYSTEM_USER_ID            = 9999;

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
        $this->table("notification")
            ->insert(
                [
                    "id"          => self::NOTIFICATION_CONTACT
                    , "name"      => self::NOTIFICATION_CONTACT_DESC
                    , "create_ts" => time()
                ]
            )
            ->save();

        $this->table("notification_type")
            ->insert(
                [
                    "id"                => self::TYPE_MAIL
                    , "name"            => self::TYPE_MAIL_NAME
                    , "notification_id" => self::NOTIFICATION_CONTACT
                    , "mandatory"       => 1
                    , "create_ts"       => time()
                ]
            )
            ->save();

        $this->execute("delete from notification_type_user where notification_type_id = " . self::NOTIFICATION_CONTACT . ";");
        $this->execute("INSERT INTO notification_type_user (notification_type_id, user_id, active) SELECT " . self::NOTIFICATION_CONTACT . ", id, " . self::TYPE_MAIL . " FROM user where id != " . self::SYSTEM_USER_ID);

    }

}
