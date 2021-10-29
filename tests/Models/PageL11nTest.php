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

use Modules\CMS\Models\PageL11n;
use phpOMS\Localization\ISO639x1Enum;

/**
 * @internal
 */
final class PageL11nTest extends \PHPUnit\Framework\TestCase
{
    private PageL11n $l11n;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->l11n = new PageL11n();
    }

    /**
     * @covers Modules\CMS\Models\PageL11n
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->l11n->getId());
        self::assertEquals('', $this->l11n->name);
        self::assertEquals('', $this->l11n->content);
        self::assertEquals(0, $this->l11n->page);
        self::assertEquals(ISO639x1Enum::_EN, $this->l11n->getLanguage());
    }

    /**
     * @covers Modules\CMS\Models\PageL11n
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->l11n->name = 'TestName';
        self::assertEquals('TestName', $this->l11n->name);
    }

    /**
     * @covers Modules\CMS\Models\PageL11n
     * @group module
     */
    public function testContentInputOutput() : void
    {
        $this->l11n->content = 'TestContent';
        self::assertEquals('TestContent', $this->l11n->content);
    }

    /**
     * @covers Modules\CMS\Models\PageL11n
     * @group module
     */
    public function testLanguageInputOutput() : void
    {
        $this->l11n->setLanguage(ISO639x1Enum::_DE);
        self::assertEquals(ISO639x1Enum::_DE, $this->l11n->getLanguage());
    }

    /**
     * @covers Modules\CMS\Models\PageL11n
     * @group module
     */
    public function testSerialize() : void
    {
        $this->l11n->name    = 'Title';
        $this->l11n->content = 'Content';
        $this->l11n->page    = 2;
        $this->l11n->setLanguage(ISO639x1Enum::_DE);

        self::assertEquals(
            [
                'id'           => 0,
                'name'         => 'Title',
                'content'      => 'Content',
                'page'         => 2,
                'language'     => ISO639x1Enum::_DE,
            ],
            $this->l11n->jsonSerialize()
        );
    }
}
