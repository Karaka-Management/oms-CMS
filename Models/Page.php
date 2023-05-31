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

use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\NullBaseStringL11n;

/**
 * Page class.
 *
 * @package Modules\CMS\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
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
    public int $id = 0;

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
     * @var BaseStringL11n[]
     * @since 1.0.0
     */
    private array $l11n = [];

    /**
     * Page template.
     *
     * @var string
     * @since 1.0.0
     */
    public string $template = '';

    /**
     * App.
     *
     * @var int
     * @since 1.0.0
     */
    public int $app = 0;

    /**
     * Add l11n
     *
     * @param BaseStringL11n $l11n Page l11n
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addL11n(BaseStringL11n $l11n) : void
    {
        $this->l11n[] = $l11n;
    }

    /**
     * Get l11n
     *
     * @param null|string $name Localization name
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    public function getL11n(string $name = null) : BaseStringL11n
    {
        foreach ($this->l11n as $l11n) {
            if ($l11n->name === $name) {
                return $l11n;
            }
        }

        return new NullBaseStringL11n();
    }

    /**
     * Get localizations
     *
     * @return BaseStringL11n[]
     *
     * @since 1.0.0
     */
    public function getL11ns() : array
    {
        return $this->l11n;
    }
}
