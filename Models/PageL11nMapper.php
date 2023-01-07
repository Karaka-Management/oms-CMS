<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\CMS\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\Localization\BaseStringL11n;

/**
 * CMS mapper class.
 *
 * @package Modules\CMS\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class PageL11nMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'cms_page_l11n_id'       => ['name' => 'cms_page_l11n_id',       'type' => 'int',    'internal' => 'id'],
        'cms_page_l11n_name'     => ['name' => 'cms_page_l11n_name',     'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'cms_page_l11n_content'  => ['name' => 'cms_page_l11n_content',  'type' => 'string', 'internal' => 'content'],
        'cms_page_l11n_page'     => ['name' => 'cms_page_l11n_page',     'type' => 'int',    'internal' => 'ref'],
        'cms_page_l11n_language' => ['name' => 'cms_page_l11n_language', 'type' => 'string', 'internal' => 'language'],
        'cms_page_l11n_country' => ['name' => 'cms_page_l11n_country', 'type' => 'string', 'internal' => 'country'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'cms_page_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='cms_page_l11n_id';

    /**
     * Model to use by the mapper.
     *
     * @var string
     * @since 1.0.0
     */
    public const MODEL = BaseStringL11n::class;
}
