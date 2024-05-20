<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\CMS\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Account status enum.
 *
 * @package Modules\CMS\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class PageStatus extends Enum
{
    public const ACTIVE = 1;

    public const INACTIVE = 2;
}
