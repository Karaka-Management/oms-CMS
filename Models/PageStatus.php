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
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\CMS\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Account status enum.
 *
 * @package Modules\CMS\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class PageStatus extends Enum
{
    public const ACTIVE = 1;

    public const INACTIVE = 2;
}
