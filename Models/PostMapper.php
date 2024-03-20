<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
 * @template T of Post
 * @extends DataMapperFactory<T>
 */
final class PostMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'cms_post_id'     => ['name' => 'cms_post_id',       'type' => 'int',    'internal' => 'id'],
        'cms_post_name'   => ['name' => 'cms_post_name',     'type' => 'string', 'internal' => 'name'],
        'cms_post_status' => ['name' => 'cms_post_status',   'type' => 'int',    'internal' => 'status'],
        'cms_post_app'    => ['name' => 'cms_post_app',      'type' => 'int',    'internal' => 'app'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'cms_post';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'cms_post_id';
}
