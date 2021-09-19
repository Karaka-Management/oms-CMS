<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Admin\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Admin\Models;

/**
 * Page class.
 *
 * @package Modules\Admin\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class Page
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    private int $id = 0;

    /**
     * Page name.
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Page status.
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = PageStatus::ACTIVE;

    /**
     * Page localization
     *
     * @var PageL11n
     * @since 1.0.0
     */
    protected PageL11n $l11n;

    /**
     * Page template.
     *
     * @var string
     * @since 1.0.0
     */
    public string $template;

    /**
     * App.
     *
     * @var int
     * @since 1.0.0
     */
    private int $app = 0;

    /**
     * Construct.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->$l11n = new NullPageL11n();
    }
}
