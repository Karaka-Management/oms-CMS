<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\tests\Models;

use Modules\CMS\Models\Page;
use Modules\CMS\Models\PageL11nMapper;
use Modules\CMS\Models\PageMapper;
use phpOMS\Localization\BaseStringL11n;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\CMS\Models\PageMapper::class)]
final class PageMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testCR() : void
    {
        $page           = new Page();
        $page->name     = 'internal_page_name';
        $page->template = 'tpl';
        $page->app      = 1;

        $id = PageMapper::create()->execute($page);
        self::assertGreaterThan(0, $page->id);
        self::assertEquals($id, $page->id);

        $l11n       = new BaseStringL11n('Test Page');
        $l11n->name = 'test_name';
        $l11n->ref  = $id;

        PageL11nMapper::create()->execute($l11n);
        $page->addL11n($l11n);

        $pageR = PageMapper::get()
            ->with('l11n')
            ->where('id', $page->id)
            ->execute();

        self::assertEquals($page->name, $pageR->name);
        self::assertEquals($page->template, $pageR->template);
        self::assertEquals($page->app, $pageR->app);
        self::assertEquals($page->status, $pageR->status);
        self::assertEquals($page->getL11n('test_name')->name, $pageR->getL11n('test_name')->name);
        self::assertEquals($page->getL11n('test_name')->content, $pageR->getL11n('test_name')->content);
    }
}
