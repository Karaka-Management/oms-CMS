<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
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
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\CMS\Models\Page', new NullPage());
    }

    /**
     * @covers Modules\CMS\Models\NullPage
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullPage(2);
        self::assertEquals(2, $null->getId());
    }
}
