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

namespace Didapptic\Repository;

use DateTime;
use doganoo\Backgrounder\BackgroundJob\Job;
use doganoo\Backgrounder\BackgroundJob\JobList;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

/**
 * Class BackgroundJobManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class BackgroundJobRepository {

    private $connector = null;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function replaceJobs(JobList $jobList): bool {
        $inserted = true;

        /** @var Job $job */
        foreach ($jobList as $job) {
            $inserted = $this->replaceJob($job);
        }

        return $inserted;
    }

    public function replaceJob(Job $job): bool {
        if (true === $this->hasJob($job)) {
            return $this->updateJob($job);
        }
        return $this->insert($job);
    }

    private function hasJob(Job $job): bool {

        /** @var Job $listJob */
        foreach ($this->getJobList() as $listJob) {
            if ($job->getName() === $listJob->getName()) return true;
        }

        return false;

    }

    public function getJobList(): JobList {

        $list      = new JobList();
        $sql       = "SELECT
                    b.`id`
                    , b.`name`
                    , b.`interval`
                    , b.`type`
                    , b.`last_run`
                    , b.`info`
                    , b.`create_ts`
                FROM background_job b;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return $list;
        $statement->execute();
        $list = new JobList();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {

            $id       = (int) $row[0];
            $name     = $row[1];
            $interval = (int) $row[2];
            $type     = $row[3];
            $lastRun  = $row[4];
            $info     = $row[5];
            $createTs = $row[6];

            $info = null === $info
                ? null
                : json_decode($info, true);

            $job = new Job();
            $job->setId($id);
            $job->setName($name);
            $job->setInterval($interval);
            $job->setType($type);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $lastRun);
            $job->setLastRun(
                $dateTime
            );
            $job->setInfo($info);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $createTs);
            $job->setCreateTs(
                $dateTime
            );
            $list->add($job);
        }
        return $list;
    }

    public function updateJob(Job $job): bool {
        $sql = "
                update `background_job`
                    set `name`      = :name
                      , `interval`  = :interval
                      , `type`      = :type
                      , `last_run`  = :last_run
                      , `info`      = :info
                    where `id` = :id;
        ";

        $statement = $this->connector->prepare($sql);

        if (null === $statement) {
            return false;
        }

        $id       = $job->getId();
        $name     = $job->getName();
        $interval = $job->getInterval();
        $type     = $job->getType();
        $lastRun  = $job->getLastRun();
        $info     = $job->getInfo();

        $lastRun = null === $lastRun
            ? null
            : $lastRun->getTimestamp();

        $info = null === $info
            ? null
            : json_encode($info);

        $statement->bindParam(":name", $name);
        $statement->bindParam(":interval", $interval);
        $statement->bindParam(":type", $type);
        $statement->bindParam(":last_run", $lastRun);
        $statement->bindParam(":info", $info);
        $statement->bindParam(":id", $id);

        $statement->execute();

        return $statement->rowCount() > 0;

    }

    private function insert(Job $job): bool {
        $sql = "insert into `background_job` (
                  `name`
                  , `type`
                  , `last_run`
                  , `info`
                  , `create_ts`
                  , `interval`
                  )
                  values (
                          :name
                          , :type
                          , :last_run
                          , :info
                          , :create_ts
                          , :interval
                          );";

        $statement = $this->connector->prepare($sql);

        $name     = $job->getName();
        $type     = $job->getType();
        $lastRun  = $job->getLastRun();
        $lastRun  = null === $lastRun
            ? null
            : $lastRun->getTimestamp();
        $info     = $job->getInfo();
        $info     = null === $info
            ? null
            : json_encode($info);
        $createTs = $job->getCreateTs();
        $createTs = $createTs->getTimestamp();
        $interval = $job->getInterval();

        $statement->bindParam("name", $name);
        $statement->bindParam("type", $type);
        $statement->bindParam("last_run", $lastRun);
        $statement->bindParam("info", $info);
        $statement->bindParam("create_ts", $createTs);
        $statement->bindParam("interval", $interval);
        $executed = $statement->execute();
        if (false === $executed) return false;

        $lastInsertId = $this->connector->getLastInsertId();

        if (null === $lastInsertId) return false;

        return false === $this->hasErrors($statement->errorCode());

    }

    protected function hasErrors(string $errorCode): bool {
        return $errorCode !== "00000";
    }

    public function updateJobs(JobList $jobList): bool {
        $updated = false;

        /** @var Job $job */
        foreach ($jobList as $job) {
            $updated = $this->updateJob($job);
        }

        return $updated;
    }

}
