<?php

declare(strict_types=1);

namespace Jfcherng\Utility\Test;

use Jfcherng\Utility\MbString;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class MbStringTest extends TestCase
{
    /**
     * Test MbString.
     *
     * @covers \Jfcherng\Utility\MbString::__toString
     * @covers \Jfcherng\Utility\MbString::append
     * @covers \Jfcherng\Utility\MbString::count
     * @covers \Jfcherng\Utility\MbString::endsWith
     * @covers \Jfcherng\Utility\MbString::get
     * @covers \Jfcherng\Utility\MbString::getAt
     * @covers \Jfcherng\Utility\MbString::has
     * @covers \Jfcherng\Utility\MbString::offsetExists
     * @covers \Jfcherng\Utility\MbString::offsetGet
     * @covers \Jfcherng\Utility\MbString::offsetSet
     * @covers \Jfcherng\Utility\MbString::set
     * @covers \Jfcherng\Utility\MbString::setAt
     * @covers \Jfcherng\Utility\MbString::startsWith
     * @covers \Jfcherng\Utility\MbString::str_enclose_i
     * @covers \Jfcherng\Utility\MbString::str_insert_i
     * @covers \Jfcherng\Utility\MbString::strlen
     * @covers \Jfcherng\Utility\MbString::strpos
     * @covers \Jfcherng\Utility\MbString::substr
     * @covers \Jfcherng\Utility\MbString::substr_replace_i
     * @covers \Jfcherng\Utility\MbString::toArray
     */
    public function testMbString(): void
    {
        $str = '那我就測試一下許功蓋！';
        $mb = new MbString($str, 'UTF-8');

        $this->assertSame('那我就測試一下許功蓋！', (string) $mb);
        $this->assertSame('許', $mb[7]);
        $this->assertSame('功', $mb->getAt(8));
        $this->assertSame(11, $mb->strlen());
        $this->assertSame(11, count($mb));
        $this->assertTrue(isset($mb[10]));
        $this->assertFalse(isset($mb[11]));

        $mb->setAt(9, '德');
        $this->assertSame('那我就測試一下許功德！', $mb->get());

        $mb->set('下一位 5 號是金城武。');
        $this->assertSame('下一位 5 號是金城武。', $mb->get());
        $this->assertSame('金城武', $mb->substr(8, 3));

        // in-place substr_replace
        $mb->set('下一位 5 號是金城武。');
        $mb->substr_replace_i('不是許功蓋', 8, 3);
        $this->assertSame('下一位 5 號是不是許功蓋。', $mb->get());
        $this->assertSame(7, $mb->strpos('是'));
        $this->assertSame(9, $mb->strpos('是', 8));

        // in-place str_replace
        $mb->set('下一位一號是金城武。');
        $mb->str_replace_i('一', '七');
        $this->assertSame('下七位七號是金城武。', $mb->get());

        $mb->set('下一位 5 號是金城武。');
        $this->assertTrue($mb->has('號'));
        $this->assertFalse($mb->has('劉'));
        $this->assertTrue($mb->startsWith('下一'));
        $this->assertFalse($mb->startsWith('上'));
        $this->assertTrue($mb->endsWith('。'));
        $this->assertFalse($mb->endsWith('金城武'));

        $mb->set('下一位 5 號是金城武。');
        $mb->str_insert_i('也', 7);
        $this->assertSame('下一位 5 號也是金城武。', $mb->get());

        $mb->set('今天是五月');
        $mb->append('十六日');
        $this->assertSame('今天是五月十六日', $mb->get());

        $mb->set('明年是2018');
        $mb->str_enclose_i(['「', '」'], 3, 4);
        $this->assertSame('明年是「2018」', $mb->get());

        $mb->set('本月是五月');
        $this->assertSame(['本', '月', '是', '五', '月'], $mb->toArray());
    }
}
