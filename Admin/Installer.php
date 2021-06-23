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

use phpOMS\Module\InstallerAbstract;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use Modules\CMS\Models\Application;
use Modules\CMS\Models\ApplicationMapper;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\System\File\PathException;
use phpOMS\Uri\HttpUri;

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
     * @param DatabasePool $dbPool Database pool
     * @param array        $data   Module info
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

    	if (!isset($cmsData[0]['name'], $cmsData[0]['src'], $cmsData[0]['dest'])) {
    		return [];
    	}

        $response = new HttpResponse();
		$request  = new HttpRequest(new HttpUri(''));

		$request->header->account = 1;
		$request->setData('name', $cmsData[0]['name']);
        $request->setData('appSrc', $cmsData[0]['src']);
        $request->setData('appDest', $cmsData[0]['dest']);
        $request->setData('theme', $cmsData[0]['theme'] ?? 'Default', true);
        $app->moduleManager->get('Admin')->apiInstallApplication($request, $response);

        $app       = new Application();
        $app->name = $cmsData[0]['name'];

        ApplicationMapper::create($app);

        return [];
    }
}
