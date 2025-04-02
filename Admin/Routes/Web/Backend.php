<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\CMS\Controller\BackendController;
use Modules\CMS\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/cms/application/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^/cms/application/page/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewPageList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^/cms/application/page(\?.*$|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewPage',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^/cms/application/post/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationPosts',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^/cms/application/files(\?.*$|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationFile',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^/cms/application/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
];
