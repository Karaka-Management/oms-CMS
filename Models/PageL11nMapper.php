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
        'cms_page_l11n_id'         => ['name' => 'cms_page_l11n_id',       'type' => 'int',    'internal' => 'id'],
        'cms_page_l11n_name'       => ['name' => 'cms_page_l11n_name',    'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'cms_page_l11n_content'    => ['name' => 'cms_page_l11n_content',    'type' => 'string', 'internal' => 'content'],
        'cms_page_l11n_page'       => ['name' => 'cms_page_l11n_page',    'type' => 'int', 'internal' => 'page'],
        'cms_page_l11n_language'   => ['name' => 'cms_page_l11n_language', 'type' => 'string', 'internal' => 'language'],
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
}