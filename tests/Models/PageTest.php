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

/**
 * @internal
 */
final class PageTest extends \PHPUnit\Framework\TestCase
{
    private Page $page;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->page = new Page();
    }

    /**
     * @covers Modules\CMS\Models\Page
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->page->getId());
        self::assertEquals('', $this->page->name);
        self::assertEquals('', $this->page->template);
        self::assertEquals(0, $this->page->app);
    }

    /**
     * @covers Modules\CMS\Models\Page
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->page->name = 'TestName';
        self::assertEquals('TestName', $this->page->name);
    }

    /**
     * @covers Modules\CMS\Models\Page
     * @group module
     */
    public function testTemplateInputOutput() : void
    {
        $this->page->template = 'TestTemplate';
        self::assertEquals('TestTemplate', $this->page->template);
    }

    /**
     * @covers Modules\CMS\Models\Page
     * @group module
     */
    public function testL11nInputOutput() : void
    {
        $l11n = new PageL11n('test_name', 'value');

        $this->page->addL11n($l11n);
        self::assertEquals('value', $this->page->getL11n('test_name')->content);
    }
}
