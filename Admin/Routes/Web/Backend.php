<?php declare(strict_types=1);

use Modules\CMS\Controller\BackendController;
use Modules\CMS\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/cms/application/list.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/content.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationContents',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/file((?!tpl=).)*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationFiles',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/file\?.*?tpl=.*?$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationFile',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/create.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\BackendController:viewApplicationCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
];
