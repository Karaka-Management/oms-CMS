<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\CMS\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\Admin;

use phpOMS\Application\ApplicationAbstract;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Module\InstallerAbstract;
use phpOMS\System\File\PathException;
use phpOMS\System\File\PermissionException;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\ArrayUtils;
use phpOMS\Utils\Parser\Php\ArrayParser;

/**
 * Installer class.
 *
 * @package Modules\CMS\Admin
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * Install data from providing modules.
     *
     * @param ApplicationAbstract $app  Application
     * @param array               $data Additional data
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function installExternal(ApplicationAbstract $app, array $data) : array
    {
        if (!\is_file($data['path'] ?? '')) {
            throw new PathException($data['path'] ?? '');
        }

        $cmsFile = \file_get_contents($data['path'] ?? '');
        if ($cmsFile === false) {
            throw new PathException($data['path'] ?? ''); // @codeCoverageIgnore
        }

        $cmsData = \json_decode($cmsFile, true) ?? [];
        if ($cmsData === false) {
            throw new \Exception(); // @codeCoverageIgnore
        }

        $apiApp = new class() extends ApplicationAbstract
        {
            protected string $appName = 'Api';
        };

        $apiApp->dbPool         = $app->dbPool;
        $apiApp->unitId         = $app->unitId;
        $apiApp->accountManager = $app->accountManager;
        $apiApp->appSettings    = $app->appSettings;
        $apiApp->moduleManager  = $app->moduleManager;
        $apiApp->eventManager   = $app->eventManager;

        /** @var array $cmsData */
        foreach ($cmsData as $cms) {
            switch ($cms['type']) {
                case 'application':
                    self::installApplication($apiApp, $cms);
                    break;
                case 'page':
                    $cms['path'] = $data['path'];

                    self::installPage($apiApp, $cms);
                    break;
                case 'route':
                    self::installRoutesHooks(
                        \dirname($data['path']) . '/' . $cms['src'] . '/Routes.php',
                        __DIR__ . '/../../../Web/' . $cms['dest'] . '/Routes.php'
                    );
                    break;
                case 'nav':
                    self::installNavigation($apiApp, $cms);

                    break;
                default:
            }
        }

        return [];
    }

    /**
     * Install application.
     *
     * @param ApplicationAbstract $app  Application
     * @param array               $data Additional data
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installApplication(ApplicationAbstract $app, array $data) : void
    {
        if (!isset($data['name'], $data['src'], $data['dest'])) {
            return;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('name', $data['name']);
        $request->setData('theme', $data['theme'] ?? 'Default', true);

        $app->moduleManager->get('CMS')->apiApplicationInstall($request, $response);
    }

    /**
     * Install application page.
     *
     * @param ApplicationAbstract $app  Application
     * @param array               $data Additional data
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installPage(ApplicationAbstract $app, array $data) : void
    {
        if (!isset($data['id'], $data['src'])) {
            return;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('name', $data['id']);
        $request->setData('app', $data['app'] ?? 2);
        $app->moduleManager->get('CMS')->apiPageCreate($request, $response);

        $responseData = $response->get('');
        if (!\is_array($responseData)) {
            return;
        }

        $id = $responseData['response']->getId();

        $l11ns = \scandir(\dirname($data['path']) . '/' . $data['src']);

        if ($l11ns === false) {
            return; // @codeCoverageIgnore
        }

        foreach ($l11ns as $l11n) {
            $langCode = \explode('.', $l11n)[0];

            if ($l11n === '.' || $l11n === '..') {
                continue;
            }

            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;

            $request->setData('page', $id);
            $request->setData('name', $data['id']);
            $request->setData('language', $langCode);
            $request->setDatA('content', \file_get_contents(\dirname($data['path']) . '/' . $data['src'] . '/' . $l11n));

            $app->moduleManager->get('CMS')->apiPageL11nCreate($request, $response);
        }
    }

    /**
     * Install routes.
     *
     * @param string $destRoutePath Destination route path
     * @param string $srcRoutePath  Source route path
     *
     * @return void
     *
     * @throws PermissionException
     *
     * @since 1.0.0
     */
    private static function installRoutesHooks(string $destRoutePath, string $srcRoutePath) : void
    {
        if (!\is_file($destRoutePath)) {
            \file_put_contents($destRoutePath, '<?php return [];');
        }

        if (!\is_file($srcRoutePath)) {
            return;
        }

        if (!\is_file($destRoutePath)) {
            throw new PathException($destRoutePath);
        }

        if (!\is_writable($destRoutePath)) {
            throw new PermissionException($destRoutePath);
        }

        /** @noinspection PhpIncludeInspection */
        $appRoutes = include $destRoutePath;
        /** @noinspection PhpIncludeInspection */
        $moduleRoutes = include $srcRoutePath;

        $appRoutes = \array_merge_recursive($appRoutes, $moduleRoutes);

        \file_put_contents($destRoutePath, '<?php return ' . ArrayParser::serializeArray($appRoutes) . ';', \LOCK_EX);
    }

    /**
     * Uninstall routes.
     *
     * @param string $destRoutePath Destination route path
     * @param string $srcRoutePath  Source route path
     *
     * @return void
     *
     * @throws PermissionException
     *
     * @since 1.0.0
     */
    /*
    private static function uninstallRoutesHooks(string $destRoutePath, string $srcRoutePath) : void
    {
        if (!\is_file($destRoutePath)
            || !\is_file($srcRoutePath)
        ) {
            return;
        }

        if (!\is_writable($destRoutePath)) {
            throw new PermissionException($destRoutePath);
        }

        // @noinspection PhpIncludeInspection
        $appRoutes = include $destRoutePath;
        // @noinspection PhpIncludeInspection
        $moduleRoutes = include $srcRoutePath;

        $appRoutes = ArrayUtils::array_diff_assoc_recursive($appRoutes, $moduleRoutes);

        \file_put_contents($destRoutePath, '<?php return ' . ArrayParser::serializeArray($appRoutes) . ';', \LOCK_EX);
    }
    */

    /**
     * Install navigation.
     *
     * @param ApplicationAbstract $app  Application
     * @param array               $data Additional data
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installNavigation(ApplicationAbstract $app, array $data) : void
    {
        $class = '\Web\\' . $data['dest'] . '\Admin\Install\Application\Navigation';
        $class::install($app, '');
    }
}
