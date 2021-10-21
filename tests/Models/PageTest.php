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
use phpOMS\Localization\ISO639x1Enum;

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
        $this->page->l11n->name = 'L11nName';
        self::assertEquals('', $this->page->name);
        self::assertEquals('L11nName', $this->page->l11n->name);
    }
}
