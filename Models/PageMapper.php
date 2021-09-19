<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\CMS\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\CMS\Models;

use phpOMS\DataStorage\Database\DataMapperAbstract;

/**
 * CMS mapper class.
 *
 * @package Modules\CMS\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class PageL11nMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'cms_page_id'        => ['name' => 'cms_page_id',       'type' => 'int',    'internal' => 'id'],
        'cms_page_name'      => ['name' => 'cms_page_name',    'type' => 'string', 'internal' => 'name'],
        'cms_page_status'    => ['name' => 'cms_page_status',    'type' => 'int', 'internal' => 'status'],
        'cms_page_app'       => ['name' => 'cms_page_app',    'type' => 'int', 'internal' => 'app'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'cms_page_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'cms_page_l11n_id';

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'l11n' => [
            'mapper'            => self::class,
            'table'             => 'cms_page_l11n',
            'self'              => 'cms_page_l11n_page',
            'conditional'       => true,
            'external'          => null,
        ],
    ];
}
