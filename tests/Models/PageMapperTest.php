<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\CMS\tests\Models;

use Modules\CMS\Models\Page;
use Modules\CMS\Models\PageMapper;
use phpOMS\Localization\ISO639x1Enum;

/**
 * @internal
 */
final class PageMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\CMS\Models\PageMapper
     * @group module
     */
    public function testCR() : void
    {
        $page                    = new Page();
        $page->name              = 'internal_page_name';
        $page->template              = 'tpl';
        $page->app              = 1;
        $page->l11n->name        = 'Test Page';
        $page->l11n->content = 'Test content';

        $id = PageMapper::create($page);
        self::assertGreaterThan(0, $page->getId());
        self::assertEquals($id, $page->getId());

        $pageR = PageMapper::with('language', ISO639x1Enum::_EN)::get($page->getId());
        self::assertEquals($page->name, $pageR->name);
        self::assertEquals($page->template, $pageR->template);
        self::assertEquals($page->app, $pageR->app);
        self::assertEquals($page->status, $pageR->status);
        self::assertEquals($page->l11n->name, $pageR->l11n->name);
        self::assertEquals($page->l11n->content, $pageR->l11n->content);
    }
}