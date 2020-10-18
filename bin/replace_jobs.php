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

use Didapptic\BackgroundJob\Task\ActionNotifier;
use Didapptic\BackgroundJob\Task\OneTime\AppLastUpdatedClearer;
use Didapptic\BackgroundJob\Task\OneTime\CollationAndCharsetJob;
use Didapptic\BackgroundJob\Task\OneTime\CommentMigratorJob;
use Didapptic\BackgroundJob\Task\TokenCleanUp;
use Didapptic\BackgroundJob\Task\UpdateAndroid;
use Didapptic\BackgroundJob\Task\UpdateIos;
use Didapptic\Didapptic;
use Didapptic\Object\Menu;
use Didapptic\Object\Permission;
use Didapptic\Repository\BackgroundJobRepository;
use Didapptic\Repository\MenuRepository;
use doganoo\Backgrounder\BackgroundJob\Job;

require_once "../src/Didapptic.php";
require_once "../vendor/autoload.php";

$didapptic = new Didapptic();

/** @var BackgroundJobRepository $backgroundJobRepository */
$backgroundJobRepository = Didapptic::getServer()->query(BackgroundJobRepository::class);
/** @var MenuRepository $menuRepository */
$menuRepository = Didapptic::getServer()->query(MenuRepository::class);

$jobs = $backgroundJobRepository->getJobList();

/** @var Job $job */
foreach ($jobs as $job) {

    switch ($job->getName()) {
        case "BackgroundJob\TokenCleanUp":
            $job->setName(TokenCleanUp::class);
            $job->setInterval((int) 5 * 60 * 60);
            break;
        case "BackgroundJob\OneTimeJob\CommentMigratorJob":
            $job->setName(CommentMigratorJob::class);
            $job->setType(Job::JOB_TYPE_ONE_TIME);
            break;
        case "BackgroundJob\OneTimeJob\CollationAndCharsetJob":
            $job->setName(CollationAndCharsetJob::class);
            $job->setType(Job::JOB_TYPE_ONE_TIME);
            break;
        case "BackgroundJob\UpdateAndroid":
            $job->setName(UpdateAndroid::class);
            $job->setInterval((int) 24 * 60 * 60);
            break;
        case "BackgroundJob\UpdateIos":
            $job->setName(UpdateIos::class);
            $job->setInterval((int) 24 * 60 * 60);
            break;
        case "BackgroundJob\OneTimeJob\AppLastUpdatedClearer":
            $job->setName(AppLastUpdatedClearer::class);
            $job->setType(Job::JOB_TYPE_ONE_TIME);
            break;
        case "BackgroundJob\ActionNotifier":
            $job->setName(ActionNotifier::class);
            $job->setInterval((int) 0.1 * 60 * 60);
            break;
    }

    $backgroundJobRepository->updateJob($job);
}

$menuList = $menuRepository->getMenu();
$menu     = $menuList[10];

if ($menu instanceof Menu) {
    $menu->setName("Einstellungen");
    $menu->setHref("menu/settings/");
    $menu->setPermissionId(Permission::MENU_SETTINGS);
    $menuRepository->updateMenu($menu);
}
