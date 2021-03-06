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
 * Class JavaScript
 *
 * @package Didapptic\Object\Constant
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class JavaScript {

    public const ABOUT_SCRIPT          = "aboutView";
    public const CONTACT_SCRIPT        = "contactView";
    public const EDIT_APP_SCRIPT       = "editAppView";
    public const HINTS_SCRIPT          = "hintsView";
    public const LOGIN_VIEW            = "loginView";
    public const MAIN_SCRIPT           = "mainView";
    public const MATERIAL_SCRIPT       = "materialView";
    public const NEW_APP_SCRIPT        = "newAppView";
    public const NEW_USER              = "newUserView";
    public const PARTNER               = "partner";
    public const PASSWORD_LOST_SCRIPT  = "passwordLostView";
    public const PROFILE               = "profileView";
    public const RESET_PASSWORD_SCRIPT = "resetPasswordView";
    public const SETTINGS_SCRIPT       = "settingsView";

    private function __construct() {
        // in order to have a class holding
        // constants only
    }

    public function __clone() {
        // check __construct()
    }

}
