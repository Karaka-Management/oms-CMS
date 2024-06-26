<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\CMS\Controller\ApiController;
use Modules\CMS\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/cookie(\?.*|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiCookieConsent',
            'verb'       => RouteVerb::ANY,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
            ],
        ],
    ],

    '^.*/cms/application/upload$' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiApplicationInstall',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/template\?.*?tpl=.*?$' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiApplicationTemplateUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],

    '^.*/cms/page(\?.*|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiPageCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiPageUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],

    '^.*/cms/page/l11n(\?.*|$)' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiPageL11nCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiPageL11nUpdate',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::APPLICATION,
            ],
        ],
    ],
];
