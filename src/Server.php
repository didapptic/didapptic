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

namespace Didapptic;

use Closure;
use DI\Container;
use Didapptic\Backend\Decoder;
use Didapptic\Backend\Fetcher;
use Didapptic\Backend\Processor;
use Didapptic\BackgroundJob\Task\ActionNotifier;
use Didapptic\BackgroundJob\Task\OneTime\AppLastUpdatedClearer;
use Didapptic\BackgroundJob\Task\OneTime\CollationAndCharsetJob;
use Didapptic\BackgroundJob\Task\OneTime\CommentMigratorJob;
use Didapptic\BackgroundJob\Task\TokenCleanUp;
use Didapptic\BackgroundJob\Task\UpdateAndroid;
use Didapptic\BackgroundJob\Task\UpdateIos;
use Didapptic\Manager\SessionManager;
use Didapptic\Manager\Strings\FrontendManager as StringsFrontendManager;
use Didapptic\Manager\Template\FrontendManager as TemplateFrontendManager;
use Didapptic\Middleware\MaterialViewAuthentication;
use Didapptic\Middleware\RegistrationAuthentication;
use Didapptic\Object\Environment;
use Didapptic\Object\PermissionDataProvider;
use Didapptic\Object\User;
use Didapptic\Repository\App\AppCategoryRepository;
use Didapptic\Repository\App\AppRepository;
use Didapptic\Repository\App\AppSubjectRepository;
use Didapptic\Repository\App\AppTagRepository;
use Didapptic\Repository\BackgroundJobRepository;
use Didapptic\Repository\CategoryRepository;
use Didapptic\Repository\CommentRepository;
use Didapptic\Repository\DatabaseRepository;
use Didapptic\Repository\DeviceRepository;
use Didapptic\Repository\FileRepository;
use Didapptic\Repository\MaterialRepository;
use Didapptic\Repository\MenuRepository;
use Didapptic\Repository\NotificationRepository;
use Didapptic\Repository\OptionRepository;
use Didapptic\Repository\PermissionRepository;
use Didapptic\Repository\PrivacyRepository;
use Didapptic\Repository\RecommendationRepository;
use Didapptic\Repository\RequestRepository;
use Didapptic\Repository\RoleRepository;
use Didapptic\Repository\StudySubjectRepository;
use Didapptic\Repository\TagRepository;
use Didapptic\Repository\TokenRepository;
use Didapptic\Repository\URLRepository;
use Didapptic\Repository\UserRepository;
use Didapptic\Service\App\Metadata\MetadataService;
use Didapptic\Service\App\Update\UpdateAppService;
use Didapptic\Service\Application\I18N\TranslationService;
use Didapptic\Service\Application\Menu\MenuService;
use Didapptic\Service\Application\WordPress\WordPressService;
use Didapptic\Service\Asset\Less\Compiler;
use Didapptic\Service\Asset\Web\Fetcher\Fetcher as CSSFetcher;
use Didapptic\Service\DataRequest\AppMonstaAppRequest;
use Didapptic\Service\DataRequest\iTunesAppRequest;
use Didapptic\Service\File\Mime\MimeService;
use Didapptic\Service\HTTP\URL\URLService;
use Didapptic\Service\Installation\Dir\DirService;
use Didapptic\Service\Installation\Files\FileService;
use Didapptic\Service\Installation\Installer;
use Didapptic\Service\Installation\Step\MissingDirs;
use Didapptic\Service\Installation\Step\MissingFiles;
use Didapptic\Service\Installation\Step\NonWritableFiles;
use Didapptic\Service\Installation\Step\PropertyFile;
use Didapptic\Service\Material\MaterialService;
use Didapptic\Service\Notification\Config\Email\EmailService;
use Didapptic\Service\Notification\Mapper;
use Didapptic\Service\Notification\NotificationService;
use Didapptic\Service\Notification\Participant\ReceiverService;
use Didapptic\Service\Notification\Participant\SenderService;
use Didapptic\Service\Session\SessionHandler;
use Didapptic\Service\Session\SessionService;
use Didapptic\Service\Slim\Response\ResponseService;
use Didapptic\Service\Template\TemplateRenderer;
use Didapptic\Service\User\Permission\PermissionService;
use Didapptic\Service\User\Register\TokenService;
use Didapptic\Service\User\Role\RoleService;
use Didapptic\Service\User\UserService;
use doganoo\Backgrounder\Backgrounder;
use doganoo\Backgrounder\Service\Log\ILoggerService as IBackgrounderLoggerService;
use doganoo\IN\Handler\Applicant\Mail\MailApplicant;
use doganoo\IN\Handler\NotificationHandler;
use doganoo\INotify\Service\Log\ILoggerService;
use doganoo\INotify\Service\Mapper\IMapper;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPAlgorithms\Datastructure\Maps\HashMap;
use doganoo\PHPUtil\FileSystem\DirHandler;
use doganoo\PHPUtil\HTTP\Session;
use doganoo\PHPUtil\Log\FileLogger;
use doganoo\PHPUtil\Log\Logger;
use doganoo\PHPUtil\Service\DateTime\DateTimeService;
use doganoo\PHPUtil\Storage\PDOConnector;
use doganoo\PHPUtil\System\Properties;
use doganoo\SimpleRBAC\Handler\PermissionHandler;
use GuzzleHttp\Client;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Loader\FilesystemLoader;

/**
 * Class Server
 *
 * @package Didapptic
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Server {

    public const FILE_PROPERTY_SAMPLE = "file.property.sample";
    public const USER_HASH_MAP        = 'map.hash.user';
    public const APP_LIST             = 'list.app';

    /** @var string */
    private $appRoot;
    /** @var Container */
    private $container;
    /** @var HashMap|null */
    private $users = null;
    /** @var ArrayList|null */
    private $appList = null;

    public function __construct(string $appRoot) {
        $this->appRoot   = $appRoot;
        $this->container = new Container();

        $this->register(Properties::class, function () {
            return new Properties(
                $this->getSysPropertiesRoot() . "sys.properties"
            );
        });

        $this->register(Server::USER_HASH_MAP, function () {
            if (null !== $this->users) return $this->users;

            $this->users = new HashMap();
            /** @var UserRepository $userManager */
            $userManager = $this->query(UserRepository::class);

            $users = $userManager->getAll();

            if (true === $users->isEmpty()) return $this->users;

            /** @var User $user */
            foreach ($users as $user) {
                $this->users->put(
                    $user->getId()
                    , $user
                );
            }
            return $this->users;
        });

        $this->register(Server::APP_LIST, function () {
            if (null !== $this->appList) return $this->appList;

            /** @var AppRepository $appRepository */
            $appRepository = $this->query(AppRepository::class);
            $this->appList = $appRepository->getAll();

            return $this->appList;
        });

        $this->register(Server::FILE_PROPERTY_SAMPLE, function () {
            return new Properties(
                $this->getSysPropertiesRoot() . "sys.properties-sample"
            );
        });

        $this->register(Environment::class, function () {
            return new Environment(
                $this->query(Properties::class)
            );
        });

        $this->register(PDOConnector::class, function () {
            /** @var Environment $environment */
            $environment = $this->query(Environment::class);
            $properties  = $environment->getDatabaseProperties();

            $connection = new PDOConnector();
            $connection->setCredentials($properties);
            return $connection;
        });

        $this->register(PermissionRepository::class, function () {
            return new PermissionRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(UserService::class, function () {
            return new UserService(
                $this->query(SessionService::class)
                , $this->query(UserRepository::class)
                , $this->query(TokenRepository::class)
                , $this->query(RoleService::class)
            );
        });

        $this->register(Processor::class, function () {
            return new Processor(
                $this->query(Fetcher::class)
                , $this->query(Decoder::class)
            );
        });

        $this->register(Fetcher::class, function () {
            return new Fetcher(
                $this->query(Client::class)
                , $this->query(Environment::class)
            );
        });

        $this->register(Decoder::class, function () {
            return new Decoder();
        });

        $this->register(PermissionDataProvider::class, function () {
            return new PermissionDataProvider(
                $this->query(PermissionRepository::class)
                , $this->query(UserService::class)
            );
        });

        $this->register(PermissionHandler::class, function () {
            return new PermissionHandler(
                $this->query(PermissionDataProvider::class)
            );
        });

        $this->register(FilesystemLoader::class, function () {
            return new FilesystemLoader();
        });

        $this->register(PHPMailer::class, function () {
            return new PHPMailer();
        });

        $this->register(RoleRepository::class, function () {
            return new RoleRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(RegistrationAuthentication::class, function () {
            return new RegistrationAuthentication(
                $this->query(Environment::class)
            );
        });

        $this->register(MaterialViewAuthentication::class, function () {
            return new MaterialViewAuthentication(
                $this->query(Environment::class)
            );
        });

        $this->register(Client::class, function () {
            return new Client();
        });

        $this->register(TokenService::class, function () {
            return new TokenService(
                $this->query(Environment::class)
            );
        });

        $this->register(WordPressService::class, function () {
            return new WordPressService(
                $this->query(Environment::class)
            );
        });

        $this->register(ResponseService::class, function () {
            return new ResponseService();
        });

        $this->register(MenuService::class, function () {
            return new MenuService(
                $this->query(Environment::class)
                , $this->query(PermissionHandler::class)
                , $this->query(PermissionService::class)
                , $this->query(URLService::class)
            );
        });

        $this->register(TranslationService::class, function () {
            return new TranslationService(TranslationService::GERMAN);
        });

        $this->register(SessionManager::class, function () {
            return new SessionManager(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(Session::class, function () {
            return new Session();
        });

        $this->register(SessionService::class, function () {
            return new SessionService(
                $this->query(Session::class)
            );
        });

        $this->register(SessionHandler::class, function () {
            return new SessionHandler(
                $this->query(SessionManager::class)
            );
        });

        $this->register(TemplateFrontendManager::class, function () {
            return new TemplateFrontendManager();
        });

        $this->register(StringsFrontendManager::class, function () {
            return new StringsFrontendManager();
        });

        $this->register(FileService::class, function () use ($appRoot) {
            return new FileService(
                $appRoot
                , $this->query(FileRepository::class)
                , $this->query(MaterialRepository::class)
                , $this->query(Environment::class)
            );
        });

        $this->register(MissingFiles::class, function () {
            return new MissingFiles(
                $this->query(FileService::class)
            );
        });

        $this->register(MissingDirs::class, function () {
            return new MissingDirs(
                $this->query(DirService::class)
            );
        });

        $this->register(DirService::class, function () use ($appRoot) {
            return new DirService(
                $appRoot
                , $this->query(Environment::class)
            );
        });

        $this->register(NonWritableFiles::class, function () {
            return new NonWritableFiles(
                $this->query(FileService::class)
            );
        });

        $this->register(PropertyFile::class, function () {
            return new PropertyFile(
                $this->query(Server::FILE_PROPERTY_SAMPLE)
                , $this->query(Properties::class)
            );
        });

        $this->register(Installer::class, function () use ($appRoot) {
            return new Installer($appRoot);
        });

        $this->register(CommentRepository::class, function () {
            return new CommentRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(RequestRepository::class, function () {
            return new RequestRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(URLRepository::class, function () {
            return new URLRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(AppRepository::class, function () {
            return new AppRepository(
                $this->query(PDOConnector::class)
                , $this->query(CommentRepository::class)
                , $this->query(URLRepository::class)
                , $this->query(TagRepository::class)
                , $this->query(CategoryRepository::class)
                , $this->query(StudySubjectRepository::class)
                , $this->query(DeviceRepository::class)
                , $this->query(DateTimeService::class)
                , $this->query(AppTagRepository::class)
                , $this->query(AppCategoryRepository::class)
                , $this->query(AppSubjectRepository::class)
            );
        });

        $this->register(AppTagRepository::class, function () {
            return new AppTagRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(AppCategoryRepository::class, function () {
            return new AppCategoryRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(AppSubjectRepository::class, function () {
            return new AppSubjectRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(DateTimeService::class, function () {
            return new DateTimeService();
        });

        $this->register(TagRepository::class, function () {
            return new TagRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(DeviceRepository::class, function () {
            return new DeviceRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(StudySubjectRepository::class, function () {
            return new StudySubjectRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(MenuRepository::class, function () {
            return new MenuRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(UserRepository::class, function () {
            return new UserRepository(
                $this->query(PDOConnector::class)
                , $this->query(StudySubjectRepository::class)
                , $this->query(RoleService::class)
            );
        });

        $this->register(CategoryRepository::class, function () {
            return new CategoryRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(OptionRepository::class, function () {
            return new OptionRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(PrivacyRepository::class, function () {
            return new PrivacyRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(RecommendationRepository::class, function () {
            return new RecommendationRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(AppMonstaAppRequest::class, function () {
            return new AppMonstaAppRequest(
                $this->query(OptionRepository::class)
                , $this->query(Environment::class)
            );
        });

        $this->register(iTunesAppRequest::class, function () {
            return new iTunesAppRequest(
                $this->query(OptionRepository::class)
                , $this->query(Environment::class)
            );
        });

        $this->register(BackgroundJobRepository::class, function () {
            return new BackgroundJobRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(NotificationService::class, function () {
            return new NotificationService(
                $this->query(NotificationRepository::class)
                , $this->query(UserService::class)
                , $this->query(EmailService::class)
                , $this->query(SenderService::class)
                , $this->query(Environment::class)
                , $this->query(ReceiverService::class)
            );
        });

        $this->register(DatabaseRepository::class, function () {
            return new DatabaseRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(TokenRepository::class, function () {
            return new TokenRepository(
                $this->query(PDOConnector::class),
                $this->query(UserRepository::class)
            );
        });

        $this->register(CommentMigratorJob::class, function () {
            return new CommentMigratorJob(
                $this->query(PDOConnector::class)
                , $this->query(CommentRepository::class)
                , $this->query(AppRepository::class)
            );
        });

        $this->register(CollationAndCharsetJob::class, function () {
            return new CollationAndCharsetJob(
                $this->query(Properties::class)
                , $this->query(DatabaseRepository::class)
            );
        });

        $this->register(ResponseService::class, function () {
            return new ResponseService();
        });
        $this->register(MaterialService::class, function () {
            return new MaterialService(
                $this->query(MaterialRepository::class)
                , $this->query(MimeService::class)
            );
        });

        $this->register(FileRepository::class, function () {
            return new FileRepository(
                $this->query(PDOConnector::class)
            );
        });

        $this->register(MaterialRepository::class, function () {
            return new MaterialRepository(
                $this->query(PDOConnector::class)
                , $this->query(FileRepository::class)
            );
        });

        $this->register(TokenCleanUp::class, function () {
            return new TokenCleanUp(
                $this->query(TokenRepository::class)
            );
        });

        $this->register(UpdateAndroid::class, function () {
            return new UpdateAndroid(
                $this->query(AppRepository::class)
                , $this->query(UpdateAppService::class)
            );
        });

        $this->register(UpdateIos::class, function () {
            return new UpdateIos(
                $this->query(AppRepository::class)
                , $this->query(Environment::class)
                , $this->query(UpdateAppService::class)
            );
        });

        $this->register(AppLastUpdatedClearer::class, function () {
            return new AppLastUpdatedClearer(
                $this->query(AppRepository::class)
            );
        });

        $this->register(NotificationRepository::class, function () {
            return new NotificationRepository(
                $this->query(PDOConnector::class)
                , $this->query(UserRepository::class)
                , $this->query(ReceiverService::class)
                , $this->query(SenderService::class)
                , $this->query(PermissionService::class)
                , $this->query(DateTimeService::class)
            );
        });

        $this->register(ActionNotifier::class, function () {
            return new ActionNotifier(
                $this->query(NotificationRepository::class)
                , $this->query(RequestRepository::class)
                , $this->query(NotificationHandler::class)
                , $this->query(Environment::class)
                , $this->query(ReceiverService::class)
            );
        });

        $this->register(CSSFetcher::class, function () {
            return new CSSFetcher(
                $this->query(Client::class)
            );
        });

        $this->register(TemplateRenderer::class, function () {
            return new TemplateRenderer(
                $this->query(Environment::class)
            );
        });

        $this->register(NotificationHandler::class, function () {
            return new NotificationHandler(
                $this->query(IMapper::class)
                , $this->query(PermissionHandler::class)
            );
        });
        $this->register(IMapper::class, function () {
            return new Mapper();
        });

        $this->register(MailApplicant::class, function () {
            /** @var EmailService $mailService */
            $mailService = $this->query(EmailService::class);

            return new MailApplicant(
                $this->query(ILoggerService::class)
                , $mailService->getMailConfig()
            );
        });

        $this->register(ILoggerService::class, function () {
            return new Service\Notification\Logger();
        });

        $this->register(MetadataService::class, function () {
            return new MetadataService(
                $this->query(TagRepository::class)
                , $this->query(PrivacyRepository::class)
                , $this->query(RecommendationRepository::class)
                , $this->query(StudySubjectRepository::class)
                , $this->query(CategoryRepository::class)
            );
        });

        $this->register(Compiler::class, function () {
            return new Compiler(
                $this->query(Environment::class)
            );
        });

        $this->register(DirHandler::class, function () {
            return new DirHandler("");
        });

        $this->register(Backgrounder::class, function () {
            return new Backgrounder(
                $this->query(BackgroundJobRepository::class)->getJobList()
                , $this->query(BackgroundJob\Container\Container::class)
                , $this->query(IBackgrounderLoggerService::class)
            );
        });

        $this->register(IBackgrounderLoggerService::class, function () {
            return new BackgroundJob\Log\Logger();
        });

        $this->register(BackgroundJob\Container\Container::class, function () {
            return new BackgroundJob\Container\Container();
        });

        /** @var Environment $env */
        $env = $this->query(Environment::class);

        FileLogger::setPath($this->getLogRoot() . "/sys.log");
        FileLogger::setLogLevel((int) $env->getLogLevel());
        Logger::setLogLevel((int) $env->getLogLevel());

    }

    /**
     * @param string  $name
     * @param Closure $closure
     */
    public function register(string $name, Closure $closure): void {
        $this->container->set($name, $closure);
    }

    public function getSysPropertiesRoot(): string {
        return str_replace("//", "/", $this->getDataPath() . "/sys/");
    }

    public function getDataPath(): string {
        return str_replace("//", "/", $this->appRoot . "/data");
    }

    public function query(string $name) {
        return $this->container->get($name);
    }

    public function getLogRoot(): string {
        return str_replace("//", "/", $this->appRoot . "/data/log/");
    }

    public function getTemplatePath(): string {
        return str_replace("//", "/", $this->appRoot . "/data/template/");
    }

    public function getStylesheetPath(): string {
        return str_replace("//", "/", $this->appRoot . "/less/");
    }

    public function getStylesheetDistPath(): string {
        return str_replace("//", "/", $this->appRoot . "/src/dist/css/");
    }

    public function getFrontendStringPath(): string {
        return str_replace("//", "/", $this->appRoot . "/data/string/frontend/");
    }

    public function getMaterialPath(): string {
        return str_replace("//", "/", $this->getDataPath() . "/material");
    }

    public function getImageRoot(): string {
        return str_replace("//", "/", $this->appRoot . "/data/img");
    }

    public function getCssRoot(): string {
        return str_replace("//", "/", $this->appRoot . "/src/css");
    }

    public function getAppCachePath(): string {
        return str_replace("//", "/", $this->appRoot . "/data/bkp");
    }

    public function getBackgrounder(): Backgrounder {
        return $this->query(Backgrounder::class);
    }

    public function getUsersFromCache(): HashMap {
        return $this->query(Server::USER_HASH_MAP);
    }

    public function getAppsFromCache(): ArrayList {
        return $this->query(Server::APP_LIST);
    }

    public function clearCaches(): void {
        $this->users   = null;
        $this->appList = null;
    }

}
