<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\CMS\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * CMS mapper class.
 *
 * @package Modules\CMS\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Page
 * @extends DataMapperFactory<T>
 */
final class PageMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'cms_page_id'       => ['name' => 'cms_page_id',       'type' => 'int',    'internal' => 'id'],
        'cms_page_name'     => ['name' => 'cms_page_name',     'type' => 'string', 'internal' => 'name'],
        'cms_page_template' => ['name' => 'cms_page_template', 'type' => 'string', 'internal' => 'template'],
        'cms_page_status'   => ['name' => 'cms_page_status',   'type' => 'int',    'internal' => 'status'],
        'cms_page_app'      => ['name' => 'cms_page_app',      'type' => 'int',    'internal' => 'app'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'cms_page';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'cms_page_id';

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'      => PageL11nMapper::class,
            'table'       => 'cms_page_l11n',
            'self'        => 'cms_page_l11n_page',
            'conditional' => false,
            'external'    => null,
        ],
    ];
}
