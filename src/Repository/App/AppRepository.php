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

namespace Didapptic\Repository\App;

use DateTime;
use Didapptic\Didapptic;
use Didapptic\Object\App;
use Didapptic\Repository\CategoryRepository;
use Didapptic\Repository\CommentRepository;
use Didapptic\Repository\DeviceRepository;
use Didapptic\Repository\StudySubjectRepository;
use Didapptic\Repository\TagRepository;
use Didapptic\Repository\URLRepository;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Service\DateTime\DateTimeService;
use doganoo\PHPUtil\Storage\PDOConnector;
use doganoo\PHPUtil\Util\DateTimeUtil;
use PDO;
use function intval;
use function time;

/**
 * Class AppManager
 *
 * @package Didapptic\Repository
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class AppRepository {

    /** @var PDOConnector */
    private $connector;
    /** @var CommentRepository */
    private $commentRepository;
    /** @var URLRepository */
    private $urlRepository;
    /** @var TagRepository */
    private $tagRepository;
    /** @var CategoryRepository */
    private $categoryRepository;
    /** @var StudySubjectRepository */
    private $studySubjectRepository;
    /** @var DeviceRepository */
    private $deviceRepository;
    /** @var DateTimeService */
    private $dateTimeService;
    /** @var AppTagRepository */
    private $appTagRepository;
    /** @var AppCategoryRepository */
    private $appCategoryRepository;
    /** @var AppSubjectRepository */
    private $appSubjectRepository;

    public function __construct(
        PDOConnector $connector
        , CommentRepository $remarkManager
        , URLRepository $URLManager
        , TagRepository $tagManager
        , CategoryRepository $categoryManager
        , StudySubjectRepository $subjectManager
        , DeviceRepository $deviceManager
        , DateTimeService $dateTimeService
        , AppTagRepository $appTagRepository
        , AppCategoryRepository $appCategoryRepository
        , AppSubjectRepository $appSubjectRepository
    ) {
        $this->connector              = $connector;
        $this->commentRepository      = $remarkManager;
        $this->urlRepository          = $URLManager;
        $this->tagRepository          = $tagManager;
        $this->categoryRepository     = $categoryManager;
        $this->studySubjectRepository = $subjectManager;
        $this->deviceRepository       = $deviceManager;
        $this->dateTimeService        = $dateTimeService;
        $this->appTagRepository       = $appTagRepository;
        $this->appCategoryRepository  = $appCategoryRepository;
        $this->appSubjectRepository   = $appSubjectRepository;
        $this->connector->connect();
        $this->connector->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function hasUpdates(App $app): bool {
        $sql       = "SELECT 
                        a.`last_update` 
                FROM `app` a
                  WHERE a.`store_id` = :storeId 
                    AND a.`operating_system` = :operating_system;";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $storeId         = $app->getStoreId();
        $operatingSystem = $app->getOperatingSystem();
        $statement->bindParam(":storeId", $storeId);
        $statement->bindParam(":operating_system", $operatingSystem);
        $statement->execute();
        if ($statement->rowCount() === 0) return false;
        $row           = $statement->fetch(PDO::FETCH_BOTH);
        $lastStoreTs   = $row[0];
        $lastStoreTs   = intval($lastStoreTs);
        $appLastUpdate = intval($app->getLastUpdated()->getTimestamp());
        return $lastStoreTs !== $appLastUpdate;
    }

    public function insertAll(ArrayList $list): bool {
        $inserted = false;
        /** @var App $app */
        foreach ($list as $app) {
            $inserted = $this->insert($app);
        }
        return $inserted;
    }

    public function insert(App $app): bool {
        $sql       = "INSERT INTO app (
                                    `store_id`
                                  , `price`
                                  , `currency`
                                  , `description`
                                  , `version`
                                  , `last_update`
                                  , `store_rating`
                                  , `release_date`
                                  , `store_url`
                                  , `author_rating`
                                  , `results_quality`
                                  , `usage`
                                  , `presentability`
                                  , `name`
                                  , `operating_system`
                                  , `minimum_os_version`
                                  , `developer`
                                  , `developer_website`
                                  , `author`
                                  , `recommendation`
                                  , `privacy`
                                  , `delete_ts`
                                  , `create_ts`
                                  )
                      VALUES (
                                    :store_id
                                  , :price
                                  , :currency
                                  , :description
                                  , :version
                                  , :last_store_update_ts
                                  , :rating
                                  , :release_date
                                  , :store_url
                                  , :rating_author
                                  , :results_quality
                                  , :usage
                                  , :presentability
                                  , :name
                                  , :operating_system
                                  , :minimum_os_version
                                  , :developer
                                  , :developer_website
                                  , :author
                                  , :recommendation
                                  , :privacy
                                  , :delete_ts
                                  , :create_ts
                                  );";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return false;
        }
        $this->connector->startTransaction();
        $store_id          = $app->getStoreId();
        $price             = (float) $app->getPrice();
        $currency          = (string) $app->getPriceCurrency();
        $description       = (string) $app->getDescription();
        $version           = (string) $app->getVersion();
        $lastStoreUpdateTs = $app->getLastUpdated();
        $lastStoreUpdateTs = (int) $lastStoreUpdateTs->getTimestamp();
        $rating            = (float) $app->getRating();
        $releaseDate       = $app->getReleaseDate();
        $releaseDate       = (int) $releaseDate->getTimestamp();
        $storeUrl          = (string) $app->getStoreURL();
        $ratingAuthor      = -1;
        $resultsQuality    = $app->getResultsQuality();
        $usage             = $app->getUsage();
        $presentability    = $app->getPresentability();
        $name              = (string) $app->getName();
        $operatingSystem   = (int) $app->getOperatingSystem();
        $minimumOsVersion  = (string) $app->getMinimumOsVersion();
        $developer         = (string) $app->getDeveloper();
        $developerWebsite  = (string) $app->getDeveloperWebsite();
        $author            = (int) $app->getAuthor();
        $recommendation    = (int) $app->getRecommendation();
        $privacy           = (int) $app->getPrivacy();
        $deleteTs          = null;
        $createTs          = DateTimeUtil::getUnixTimestamp();

        $statement->bindParam(":store_id", $store_id);
        $statement->bindParam(":price", $price);
        $statement->bindParam(":currency", $currency);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":version", $version);
        $statement->bindParam(":last_store_update_ts", $lastStoreUpdateTs);
        $statement->bindParam(":rating", $rating);
        $statement->bindParam(":release_date", $releaseDate);
        $statement->bindParam(":store_url", $storeUrl);
        $statement->bindParam(":rating_author", $ratingAuthor);
        $statement->bindParam(":results_quality", $resultsQuality);
        $statement->bindParam(":usage", $usage);
        $statement->bindParam(":presentability", $presentability);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":operating_system", $operatingSystem);
        $statement->bindParam(":minimum_os_version", $minimumOsVersion);
        $statement->bindParam(":developer", $developer);
        $statement->bindParam(":developer_website", $developerWebsite);
        $statement->bindParam(":author", $author);
        $statement->bindParam(":recommendation", $recommendation);
        $statement->bindParam(":privacy", $privacy);
        $statement->bindParam(":delete_ts", $deleteTs);
        $statement->bindParam(":create_ts", $createTs);
        $statement->execute();

        $appId            = (int) $this->connector->getConnection()->lastInsertId();
        $tags             = $app->getTags();
        $categories       = $app->getCategories();
        $subjects         = $app->getSubjects();
        $supportedDevices = $app->getSupportedDevices();


        foreach ($app->getUrls() as $key => $url) {
            $this->urlRepository->insert($appId, $url, $key, (new DateTime())->getTimestamp());
        }

        $this->commentRepository->insertComment(
            $app->getDidacticRemark()
            , $appId
            , CommentRepository::DIDACTIC_REMARK
        );

        $this->commentRepository->insertComment(
            $app->getDidacticComment()
            , $appId
            , CommentRepository::DIDACTIC_COMMENT
        );

        $this->commentRepository->insertComment(
            $app->getPrivacyComment()
            , $appId
            , CommentRepository::PRIVACY_COMMENT
        );

        foreach ($tags as $tag) {
            if ($this->tagRepository->exists($tag)) {
                $this->appTagRepository->addTag($appId, (int) $tag);
            }
        }
        foreach ($categories as $category) {
            if ($this->categoryRepository->exists($category)) {
                $this->appCategoryRepository->addCategory($appId, (int) $category);
            }
        }
        foreach ($subjects as $subject) {
            if ($this->studySubjectRepository->exists($subject)) {
                $this->appSubjectRepository->addSubject($appId, (int) $subject);
            }
        }
        foreach ($supportedDevices as $supportedDevice) {
            if ($this->deviceRepository->exists($supportedDevice)) {
                $this->deviceRepository->add($appId, $supportedDevice);
            }
        }

        if (true === $app->isIos()) {
            $this->urlRepository->insert(
                $appId
                , $app->getIosPrivacy()
                , "ios_privacy"
                , (new DateTime())->getTimestamp()
            );
        }

        $this->connector->commit();
        Didapptic::getServer()->clearCaches();
        return true;
    }

    public function updateAll(ArrayList $apps): bool {
        $updated = false;
        /** @var App $app */
        foreach ($apps as $app) {
            $updated = $this->update($app);
            FileLogger::debug("{$app->getStoreId()} updated: $updated");
        }
        return $updated;
    }

    public function update(App $app): bool {
        $this->connector->startTransaction();
        $sql = "UPDATE `app` SET
                                   `store_url` = :store_url
                                  , `price` = :price
                                  , `store_rating` = :rating
                                  , `author_rating` = :author_rating
                                  , `results_quality` = :results_quality
                                  , `usage` = :usage
                                  , `presentability` = :presentability
                                  , `description` = :description
                                  , `version` = :version
                                  , `last_update` = :last_update
                                  , `name` = :name
                                  , `operating_system` = :operating_system
                                  , `currency` = :currency
                                  , `minimum_os_version` = :minimum_os_version
                                  , `developer_website` = :developer_website
                                  , `developer` = :developer
                                  , `release_date` = :release_date
                                  , `author` = :author
                                  , `recommendation` = :recommendation
                                  , `privacy` = :privacy
                                  , `last_user_update_ts` = :last_user_update_ts
                                  , `delete_ts` = :delete_ts
                                  , `create_ts` = :create_ts
                                  , `store_id` = :store_id
                        WHERE `id` = :id;";

        $statement = $this->connector->prepare($sql);

        $appId            = $app->getId();
        $storeURL         = $app->getStoreURL();
        $price            = (float) $app->getPrice();
        $rating           = $app->getRating();
        $authorRating     = $app->getRating();
        $resultsQuality   = $app->getResultsQuality();
        $usage            = $app->getUsage();
        $presentability   = $app->getPresentability();
        $description      = $app->getDescription();
        $version          = $app->getVersion();
        $lastUpdate       = $app->getLastUpdated()->getTimestamp();
        $name             = $app->getName();
        $operatingSystem  = $app->getOperatingSystem();
        $currency         = $app->getPriceCurrency();
        $minimumOsVersion = $app->getMinimumOsVersion();
        $developerWebsite = $app->getDeveloperWebsite();
        $developer        = $app->getDeveloper();
        $author           = $app->getAuthor();
        $recommendation   = $app->getRecommendation();
        $privacy          = $app->getPrivacy();
        $deleteTs         = (null === $app->getDeleteTs())
            ? null
            : $app->getDeleteTs()->getTimestamp();
        $releaseDate      = (null === $app->getReleaseDate())
            ? null
            : $app->getReleaseDate()->getTimestamp();
        $createTs         = (null === $app->getCreateTs())
            ? 0
            : $app->getCreateTs()->getTimestamp();
        $lastUserUpdateTs = (null === $app->getLastUserUpdateTs())
            ? null
            : $app->getLastUserUpdateTs()->getTimestamp();
        $storeId          = $app->getStoreId();

        $statement->bindParam(":store_url", $storeURL);
        $statement->bindParam(":price", $price);
        $statement->bindParam(":rating", $rating);
        $statement->bindParam(":author_rating", $authorRating);
        $statement->bindParam(":results_quality", $resultsQuality);
        $statement->bindParam(":usage", $usage);
        $statement->bindParam(":presentability", $presentability);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":version", $version);
        $statement->bindParam(":last_update", $lastUpdate);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":operating_system", $operatingSystem);
        $statement->bindParam(":currency", $currency);
        $statement->bindParam(":minimum_os_version", $minimumOsVersion);
        $statement->bindParam(":developer_website", $developerWebsite);
        $statement->bindParam(":developer", $developer);
        $statement->bindParam(":release_date", $releaseDate);
        $statement->bindParam(":author", $author);
        $statement->bindParam(":recommendation", $recommendation);
        $statement->bindParam(":privacy", $privacy);
        $statement->bindParam(":last_user_update_ts", $lastUserUpdateTs);
        $statement->bindParam(":delete_ts", $deleteTs);
        $statement->bindParam(":create_ts", $createTs);
        $statement->bindParam(":id", $appId);
        $statement->bindParam(":store_id", $storeId);

        $updatedApp = $statement->execute();
        $createTs   = (new DateTime())->getTimestamp();

        foreach ($app->getUrls() as $key => $url) {
            if ($this->urlRepository->exists($app->getId(), (string) $key)) {
                $updatedUrl = $this->urlRepository->update($app->getId(), $url, (string) $key, $createTs);
                $updatedApp = $updatedApp && $updatedUrl;
            } else {
                $insertedUrl = $this->urlRepository->insert($app->getId(), $url, (string) $key, $createTs);
                $updatedApp  = $insertedUrl && $updatedApp;
            }
        }

        $this->commentRepository->updateComment(
            $app->getDidacticComment()
            , $app->getId()
            , CommentRepository::DIDACTIC_COMMENT
        );

        $this->commentRepository->updateComment(
            $app->getDidacticRemark()
            , $app->getId()
            , CommentRepository::DIDACTIC_REMARK
        );

        $this->commentRepository->updateComment(
            $app->getPrivacyComment()
            , $app->getId()
            , CommentRepository::PRIVACY_COMMENT
        );

        $tagDeleted = $this->appTagRepository->deleteTagsByApp($app);
        foreach ($app->getTags() as $tag) {
            if ($this->tagRepository->exists($tag)) {
                $tagDeleted &= $this->appTagRepository->addTag($app->getId(), (int) $tag);
            }
        }

        $subjectDeleted = $this->appSubjectRepository->deleteSubjectsByApp($app);
        foreach ($app->getSubjects() as $subject) {
            if ($this->studySubjectRepository->exists($subject)) {
                $subjectDeleted &= $this->appSubjectRepository->addSubject($app->getId(), (int) $subject);
            }
        }
        $categoryDeleted = $this->appCategoryRepository->deleteCategoriesByApp($app);
        foreach ($app->getCategories() as $category) {
            if ($this->categoryRepository->exists($category)) {
                $categoryDeleted &= $this->appCategoryRepository->addCategory($app->getId(), (int) $category);
            }
        }

        if (App::IOS === $app->getOperatingSystem()) {
            $this->urlRepository->insert(
                $app->getId()
                , (string) $app->getIosPrivacy()
                , "ios_privacy"
                , time()
            );
        }

        if ($tagDeleted && $subjectDeleted && $categoryDeleted) {
            $this->connector->commit();
            Didapptic::getServer()->clearCaches();
            return true;
        }

        $this->connector->rollback();
        return $updatedApp;
    }

    public function markAppsAsDeleted(ArrayList $appIds): bool {
        $deleted = false;
        foreach ($appIds as $appId) {
            $deleted = $this->markAsDeleted($appId);
        }
        return $deleted;
    }

    private function markAsDeleted(string $appId): bool {
        $sql         = "UPDATE `app` a SET a.`release_date` = :release_date where a.`store_id` = :store_id;";
        $statement   = $this->connector->prepare($sql);
        $releaseDate = null;
        $statement->bindParam("release_date", $releaseDate);
        $statement->bindParam("store_id", $appId);
        $executed = $statement->execute();
        Didapptic::getServer()->clearCaches();
        return $executed;

    }

    public function getAppByStoreId(string $storeId, bool $includeDeleted = false) {
        $apps = $this->getAll($includeDeleted);

        /** @var App $app */
        foreach ($apps as $app) {
            if ($storeId === $app->getStoreId()) return $app;
        }
        return null;
    }

    public function getAll(bool $includeVisible = false, ?int $limit = null): ArrayList {
        $arrayList = new ArrayList();
        $sql       = "
            SELECT  
                  a.`id`                                                                                                                    # 0
                , CAST(a.`last_update` AS UNSIGNED)                                                                                         # 1
                , a.`name`                                                                                                                  # 2
                , a.`store_rating`                                                                                                          # 3
                , a.`price`                                                                                                                 # 4
                , a.`description`                                                                                                           # 5
                , a.`developer_website`                                                                                                     # 6
                , a.`developer`                                                                                                             # 7
                , a.`minimum_os_version`                                                                                                    # 8
                , a.`operating_system`                                                                                                      # 9
                , a.`store_id`                                                                                                              # 10            
                , (select `text` from `comment` c where c.`app_id` = a.`id` and c.`type` = :d_comment order by `create_ts` desc limit 1)    # 11
                , (select `text` from `comment` c where c.`app_id` = a.`id` and c.`type` = :d_remark order by `create_ts` desc limit 1)     # 12
                , a.`usage`                                                                                                                 # 13
                , a.`results_quality`                                                                                                       # 14
                , a.`presentability`                                                                                                        # 15
                , (select `text` from `comment` c where c.`app_id` = a.`id` and c.`type` = :p_comment order by `create_ts` desc limit 1)    # 16
                , a.`version`                                                                                                               # 17
                , a.`currency`                                                                                                              # 18
                , a.`store_url`                                                                                                             # 19
                , a.`release_date`                                                                                                          # 20
                , a.`privacy`                                                                                                               # 21
                , a.`author`                                                                                                                # 22
                , a.`recommendation`                                                                                                        # 23
                , a.`last_user_update_ts`                                                                                                   # 24
                , a.`create_ts`                                                                                                             # 25
                , ((a.`presentability` + a.`usage` + a.`results_quality`) / 3) as avg_rating                                                # 26 
                , a.`delete_ts`                                                                                                             # 27
            FROM `app` a
                WHERE 1 = 1
                ";

        if (false === $includeVisible) {
            $sql = $sql . " and a.`delete_ts` is null";
        }

        if (null !== $limit) {
            $sql = $sql . " limit $limit";
        }

        $sql = $sql . " order by a.`id` desc;";

        $statement = $this->connector->prepare($sql);
        if (null === $statement) {
            return $arrayList;
        }
        $remark = CommentRepository::DIDACTIC_REMARK;
        $statement->bindParam(":d_remark", $remark);
        $comment = CommentRepository::DIDACTIC_COMMENT;
        $statement->bindParam(":d_comment", $comment);
        $pComment = CommentRepository::PRIVACY_COMMENT;
        $statement->bindParam(":p_comment", $pComment);

        $statement->execute();
        if ($statement->rowCount() === 0) {
            return $arrayList;
        }
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $app   = new App();
            $appId = (int) $row[0];

            $app->setId($appId);
            $app->setUrls(
                $this->urlRepository->getScreenshotURLs($appId)
            );
            $app->setSubjects(
                $this->studySubjectRepository->getSubjectsByAppId($appId)
            );
            $app->setCategories(
                $this->categoryRepository->getCategoriesByAppId($appId)
            );
            $app->setTags(
                $this->tagRepository->getTagsByAppId($appId)
            );
            $app->setIconURL(
                (string) $this->urlRepository->getIconURL($appId)
            );
            $app->setSupportedDevices(
                $this->deviceRepository->getDevicesByAppId($appId)
            );

            $lastUpdate = $row[1];
            $app->setLastUpdated(
                $this->dateTimeService->fromTimestamp(
                    null !== $lastUpdate
                        ? (int) $lastUpdate
                        : null
                )
            );

            $releaseDate = $row[20];
            $app->setReleaseDate(
                $this->dateTimeService->fromNullableTimestamp(
                    null !== $releaseDate
                        ? (int) $releaseDate
                        : null
                )
            );

            $lastUserUpdate = $row[24];
            $app->setLastUserUpdateTs(
                $this->dateTimeService->fromNullableTimestamp(
                    null !== $lastUserUpdate
                        ? (int) $lastUserUpdate
                        : null
                )
            );

            $createTs = $row[25];
            $app->setCreateTs(
                $this->dateTimeService->fromTimestamp(
                    null !== $createTs
                        ? (int) $createTs
                        : null
                )
            );

            $deleteTs = $row[27];
            $app->setDeleteTs(
                $this->dateTimeService->fromNullableTimestamp(
                    null !== $deleteTs
                        ? (int) $deleteTs
                        : null
                )
            );

            $app->setName((string) $row[2]);
            $app->setRating((float) $row[3]);
            $app->setPrice((float) $row[4]);
            $app->setDescription((string) $row[5]);
            $app->setDeveloperWebsite((string) $row[6]);
            $app->setDeveloper((string) $row[7]);
            $app->setMinimumOsVersion((string) $row[8]);
            $app->setOperatingSystem((int) $row[9]);
            $app->setStoreId((string) $row[10]);
            $app->setDidacticComment((string) $row[11]);
            $app->setDidacticRemark((string) $row[12]);
            $app->setUsage((float) $row[13]);
            $app->setResultsQuality((float) $row[14]);
            $app->setPresentability((float) $row[15]);
            $app->setPrivacyComment((string) $row[16]);
            $app->setVersion((string) $row[17]);
            $app->setPriceCurrency((string) $row[18]);
            $app->setStoreURL((string) $row[19]);
            $app->setPrivacyCode((int) $row[21]);
            $app->setPrivacy((int) $row[21]);
            $app->setAuthor((int) $row[22]);
            $app->setRecommendation((int) $row[23]);
            $app->setAvgRating((float) $row[26]);

            if (App::IOS === $app->getOperatingSystem()) {
                $app->setIosPrivacy(
                    $this->urlRepository->getIosPrivacyURL($appId)
                );
            }

            $arrayList->add($app);
        }
        return $arrayList;
    }

    public function deleteByStoreId(string $storeId): bool {
        FileLogger::debug("going to delete $storeId");
        $sql       = "DELETE FROM `app` WHERE `store_id` = :app_id";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $statement->bindParam(":app_id", $storeId);
        $deleted = $statement->execute();
        Didapptic::getServer()->clearCaches();
        return $deleted;
    }

    public function hide(string $storeId): bool {
        $sql       = "UPDATE `app` SET `delete_ts` = :delete_ts WHERE `id` = :app_id";
        $statement = $this->connector->prepare($sql);
        if (null === $statement) return false;
        $deleteTs = (new DateTime())->getTimestamp();

        $statement->bindParam(":delete_ts", $deleteTs);
        $statement->bindParam(":app_id", $storeId);
        $hidden = $statement->execute();
        Didapptic::getServer()->clearCaches();
        return $hidden;
    }

}
