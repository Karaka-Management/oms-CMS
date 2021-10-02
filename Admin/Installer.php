<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\CMS\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\CMS\Admin;

use Modules\CMS\Models\Application;
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
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
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

        foreach ($cmsData as $cms) {
            switch ($cms['type']) {
                case 'application':
                    self::installApplication($app, $cms);
                    break;
                case 'page':
                    $cms['path'] = $data['path'];

                    self::installPage($app, $cms);
                    break;
                case 'route':
                    self::installRoutesHooks(
                        \dirname($data['path']) . '/' . $cms['src'] . '/Routes.php',
                        __DIR__ . '/../../../Web/' . $cms['dest'] . '/Routes.php'
                    );
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
        if (!isset($data['id'], $data['name'], $data['src'])
            || !\is_file(\dirname($data['path']) . '/' . $data['src'] . '/lang.php')
        ) {
            return;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('name', $data['id']);
        $request->setData('app', $data['app'] ?? 2);
        $app->moduleManager->get('CMS')->apiPageCreate($request, $response);
        $id = $response->get('')['response']->getId();

        $lang  = include \dirname($data['path']) . '/' . $data['src'] . '/lang.php';
        $l11ns = \scandir(\dirname($data['path']) . '/' . $data['src']);

        if ($l11ns === false) {
            return; // @codeCoverageIgnore
        }

        foreach ($l11ns as $l11n) {
            $langCode = \explode('.', $l11n)[0];

            if ($l11n === '.' || $l11n === '..' || (!empty($lang) && !isset($lang[$langCode]))) {
                continue;
            }

            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;

            $request->setData('page', $id);
            $request->setData('name', $lang[$langCode][$data['name']] ?? $data['name']);
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
    protected static function installRoutesHooks(string $destRoutePath, string $srcRoutePath) : void
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
    public static function uninstallRoutesHooks(string $destRoutePath, string $srcRoutePath) : void
    {
        if (!\is_file($destRoutePath)
            || !\is_file($srcRoutePath)
        ) {
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

        $appRoutes = ArrayUtils::array_diff_assoc_recursive($appRoutes, $moduleRoutes);

        \file_put_contents($destRoutePath, '<?php return ' . ArrayParser::serializeArray($appRoutes) . ';', \LOCK_EX);
    }
}
