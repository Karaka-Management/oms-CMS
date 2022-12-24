<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\tests\Models;

use Modules\CMS\Models\Page;
use Modules\CMS\Models\PageL11n;
use Modules\CMS\Models\PageMapper;
use Modules\CMS\Models\PageL11nMapper;
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
        $page           = new Page();
        $page->name     = 'internal_page_name';
        $page->template = 'tpl';
        $page->app      = 1;

        $id = PageMapper::create()->execute($page);
        self::assertGreaterThan(0, $page->getId());
        self::assertEquals($id, $page->getId());

        $l11n       = new PageL11n('test_name', 'Test Page');
        $l11n->page = $id;

        PageL11nMapper::create()->execute($l11n);
        $page->addL11n($l11n);

        $pageR = PageMapper::get()
            ->with('l11n')
            ->where('id', $page->getId())
            ->execute();

        self::assertEquals($page->name, $pageR->name);
        self::assertEquals($page->template, $pageR->template);
        self::assertEquals($page->app, $pageR->app);
        self::assertEquals($page->status, $pageR->status);
        self::assertEquals($page->getL11n('test_name')->name, $pageR->getL11n('test_name')->name);
        self::assertEquals($page->getL11n('test_name')->content, $pageR->getL11n('test_name')->content);
    }
}
