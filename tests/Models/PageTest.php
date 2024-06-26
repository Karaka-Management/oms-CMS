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
use phpOMS\Localization\BaseStringL11n;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\CMS\Models\Page::class)]
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

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDefault() : void
    {
        self::assertEquals(0, $this->page->id);
        self::assertEquals('', $this->page->name);
        self::assertEquals('', $this->page->template);
        self::assertEquals(0, $this->page->app);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testNameInputOutput() : void
    {
        $this->page->name = 'TestName';
        self::assertEquals('TestName', $this->page->name);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testTemplateInputOutput() : void
    {
        $this->page->template = 'TestTemplate';
        self::assertEquals('TestTemplate', $this->page->template);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testL11nInputOutput() : void
    {
        $l11n       = new BaseStringL11n('value');
        $l11n->name = 'test_name';

        $this->page->addL11n($l11n);
        self::assertEquals('value', $this->page->getL11n('test_name')->content);
    }
}
