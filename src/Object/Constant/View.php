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

namespace Didapptic\Object\Constant;

/**
 * Class View
 *
 * @package Didapptic\Object\Constant
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class View {

    public const RESET_PASSWORD_VIEW_ERROR = "reset_password_view_error.twig";
    public const RESET_PASSWORD_VIEW       = "reset_password_view.twig";
    public const APP_FORM_VIEW             = "app_form.twig";
    public const PROFILE_VIEW              = "profile_view.twig";
    public const ABOUT_VIEW                = "about_view.twig";
    public const NEW_USER_VIEW             = "new_user_view.twig";
    public const MAIN_VIEW                 = "main_view.twig";
    public const HINTS_VIEW_HTML           = "hints_view.twig";
    public const LOGIN_VIEW                = "login_view.twig";
    public const IMPRINT_VIEW              = "imprint_view.twig";
    public const USER_MANAGEMENT_VIEW      = "user_management_view.twig";
    public const ALERT_VIEW                = "alert_view.twig";
    public const PASSWORD_LOST_VIEW        = "password_lost.twig";
    public const HEADER_VIEW               = "header.twig";
    public const MATERIAL_VIEW             = "material_view.twig";
    public const PRIVACY_VIEW              = "privacy_view.twig";
    public const CONTACT                   = "contact.twig";
    public const PARTNER_VIEW              = "partner_view.twig";
    public const HEAD                      = "head.twig";
    public const SETTINGS_VIEW             = "settings.twig";

    private function __construct() {
        // in order to have a class holding
        // constants only
    }

    public function __clone() {
        // check __construct()
    }

}
