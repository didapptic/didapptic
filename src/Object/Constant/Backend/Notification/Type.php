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

namespace Didapptic\Object\Constant\Backend\Notification;

use doganoo\INotify\Notification\Type\IType;

/**
 * Class Type
 *
 * @package Didapptic\Object\Constant\Backend\Notification
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Type {

    public const NOTIFICATION = [
        Notification::DELETED_APP                => [
            IType::MAIL => Type::DELETED_APP_MAIL_ID
        ]
        , Notification::CONTACT_VIA_PLATFORM     => [
            IType::MAIL => Type::CONTACT_VIA_PLATFORM_MAIL_ID
        ]
        , Notification::REGISTRATION_INFORMATION => [
            IType::MAIL => Type::REGISTRATION_INFORMATION_MAIL_ID
        ]
        , Notification::PASSWORD_LOST            => [
            IType::MAIL => Type::PASSWORD_LOST_MAIL_ID
        ]
        , Notification::NEW_APP_ADDED            => [
            IType::MAIL => Type::NEW_APP_ADDED_MAIL_ID
        ]
    ];

    public const DELETED_APP_MAIL_ID              = 1;
    public const CONTACT_VIA_PLATFORM_MAIL_ID     = 2;
    public const REGISTRATION_INFORMATION_MAIL_ID = 3;
    public const PASSWORD_LOST_MAIL_ID            = 4;
    public const NEW_APP_ADDED_MAIL_ID            = 5;

    private function __construct() {
        // in order to have a class holding
        // constants only
    }

    public function __clone() {
        // check __construct()
    }

}
