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
use Modules\CMS\Models\ApplicationMapper;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Module\InstallerAbstract;

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
    public static function installExternal(DatabasePool $dbPool, array $data) : array
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

    	if (!isset($cmsData['name'], $cmsData['src'], $cmsData['dest'])) {
    		return [];
    	}

        $response = new HttpResponse();
		$request        = new HttpRequest(new HttpUri(''));

		$request->header->account = 1;
		$request->setData('name', $cmsData['name']);
        $request->setData('appSrc', $cmsData['src']);
        $request->setData('appDest', $cmsData['dest']);
        $request->setData('theme', $cmsData['theme'] ?? 'Default', true);
        \Modules\Admin\Controller\ApiController::apiInstallApplication($request, $response, []);

        $app       = new Application();
        $app->name = $cmsData['name'];

        ApplicationMapper::create($app);

        return [];
    }
}
