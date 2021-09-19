<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\CMS\Controller\ApiController;
use Modules\CMS\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/cms/application$' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiApplicationCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application\?.*?id=.*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiApplicationUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/template((?!tpl=).)*$' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiApplicationTemplateCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
    '^.*/cms/application/template\?.*?tpl=.*?$' => [
        [
            'dest'       => '\Modules\CMS\Controller\ApiController:apiApplicationTemplateUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionState::APPLICATION,
            ],
        ],
    ],
];
