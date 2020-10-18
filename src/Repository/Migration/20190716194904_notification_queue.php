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

class NotificationQueue extends AbstractMigration {

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
                "template_name"
                , "string"
                , [
                    "null" => false
                ]
            )
            ->addColumn(
                "arguments"
                , "json"
                , [
                    "null"      => true
                    , "default" => null
                ]
            )
            ->addColumn(
                "subject"
                , "string"
                , [
                    "null" => false
                ]
            )
            ->addColumn(
                "notification_type"
                , "integer"
                , [
                    "null" => false
                ]
            )
            ->addColumn(
                "executed"
                , "enum"
                , [
                    "null"      => false
                    , "values"  => [
                        "true"
                        , "false"
                    ]
                    , "default" => "false"
                ]
            )
            ->addColumn(
                "user_id"
                , "integer"
                , [
                    "null"     => false
                    , "signed" => false
                ]
            )
            ->addColumn(
                "delay"
                , "integer"
                , [
                    "null"      => true
                    , "default" => null
                ]
            )
            ->addColumn(
                "create_ts"
                , "integer"
                , [
                    "null" => false
                ]
            )
            ->addForeignKey(
                "notification_type"
                , 'notification_type'
                , 'id'
                , [
                "delete"   => 'CASCADE'
                , 'update' => 'CASCADE'
            ])
            ->addForeignKey(
                "user_id"
                , "user"
                , "id"
            )
            ->save();

        $this->table("user")
            ->insert(
                [
                    "id"            => 9999
                    , "first_name"  => 'Didapptic'
                    , "last_name"   => 'Didapptic'
                    , "name"        => 'Didapptic'
                    , "email"       => "info@didapptic.com"
                    , "password"    => sha1(md5(json_encode("no password")))
                    , "website_url" => "https://didapptic.com"
                    , "newsletter"  => 0
                    , "created_at"  => time()
                    , "updated_at"  => time()
                ]
            )
            ->save();

    }

}
