<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\tests\Models;

use Modules\CMS\Models\NullPage;

/**
 * @internal
 */
final class NullPageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\CMS\Models\NullPage
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\CMS\Models\Page', new NullPage());
    }

    /**
     * @covers Modules\CMS\Models\NullPage
     * @group module
     */
    public function testId() : void
    {
        $null = new NullPage(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers Modules\CMS\Models\NullPage
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullPage(2);
        self::assertEquals(['id' => 2], $null->jsonSerialize());
    }
}
