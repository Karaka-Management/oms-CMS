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

use Modules\CMS\Models\NullPageL11n;

/**
 * @internal
 */
final class Null extends \PHPUnit\Framework\TestCase
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
