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

use phpOMS\Localization\ISO639x1Enum;

/**
 * Page class.
 *
 * @package Modules\CMS\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class PageL11n
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Page ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $page = 0;

    /**
     * Page name.
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Content.
     *
     * @var string
     * @since 1.0.0
     */
    public string $content = '';

    /**
     * Language.
     *
     * @var string
     * @since 1.0.0
     */
    protected string $language = ISO639x1Enum::_EN;

    /**
     * Constructor.
     *
     * @param string $name    Name
     * @param string $content Content
     *
     * @since 1.0.0
     */
    public function __construct(string $name = '', string $content = '', string $language = ISO639x1Enum::_EN)
    {
        $this->name       = $name;
        $this->content    = $content;
        $this->language   = $language;
    }

    /**
     * Get id
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get language
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language Language
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setLanguage(string $language) : void
    {
        $this->language = $language;
    }
}
