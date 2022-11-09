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
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\CMS\tests\Models;

use Modules\CMS\Models\NullPageL11n;

/**
 * @internal
 */
final class NullPageL11nTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\CMS\Models\NullPageL11n
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\CMS\Models\PageL11n', new NullPageL11n());
    }

    /**
     * @covers Modules\CMS\Models\NullPageL11n
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullPageL11n(2);
        self::assertEquals(2, $null->getId());
    }
}
