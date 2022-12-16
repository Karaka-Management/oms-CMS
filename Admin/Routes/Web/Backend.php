<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\CMS\Controller\BackendController;
use Modules\CMS\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/cms/application/list.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/content.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationContents',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/file.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationFile',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/create.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
];
