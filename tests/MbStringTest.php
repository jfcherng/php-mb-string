<?php

declare(strict_types=1);

namespace Jfcherng\Utility\Test;

use Jfcherng\Utility\MbString;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @internal
 */
final class MbStringTest extends TestCase
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
     * @covers \Jfcherng\Utility\MbString::toArraySplit
     */
    public function testMbString(): void
    {
        $str = '那我就測試一下許功蓋！';
        $mb = new MbString($str, 'UTF-8');

        static::assertSame('那我就測試一下許功蓋！', (string) $mb);
        static::assertSame('許', $mb[7]);
        static::assertSame('功', $mb->getAt(8));
        static::assertSame(11, $mb->strlen());
        static::assertSame(11, \count($mb));
        static::assertTrue(isset($mb[10]));
        static::assertFalse(isset($mb[11]));

        $mb->setAt(9, '德');
        static::assertSame('那我就測試一下許功德！', $mb->get());

        $mb->set('下一位 5 號是金城武。');
        static::assertSame('下一位 5 號是金城武。', $mb->get());
        static::assertSame('金城武', $mb->substr(8, 3));

        // in-place substr_replace
        $mb->set('下一位 5 號是金城武。');
        $mb->substr_replace_i('不是許功蓋', 8, 3);
        static::assertSame('下一位 5 號是不是許功蓋。', $mb->get());
        static::assertSame(7, $mb->strpos('是'));
        static::assertSame(9, $mb->strpos('是', 8));

        // in-place str_replace
        $mb->set('下一位一號是金城武。');
        $mb->str_replace_i('一', '七');
        static::assertSame('下七位七號是金城武。', $mb->get());

        $mb->set('下一位 5 號是金城武。');
        static::assertTrue($mb->has('號'));
        static::assertFalse($mb->has('劉'));
        static::assertTrue($mb->startsWith('下一'));
        static::assertFalse($mb->startsWith('上'));
        static::assertTrue($mb->endsWith('。'));
        static::assertFalse($mb->endsWith('金城武'));

        $mb->set('下一位 5 號是金城武。');
        $mb->str_insert_i('也', 7);
        static::assertSame('下一位 5 號也是金城武。', $mb->get());

        $mb->set('今天是五月');
        $mb->append('十六日');
        static::assertSame('今天是五月十六日', $mb->get());

        $mb->set('明年是2018');
        $mb->str_enclose_i(['「', '」'], 3, 4);
        static::assertSame('明年是「2018」', $mb->get());

        $mb->set("本月是五月\n！");
        static::assertSame(['本', '月', '是', '五', '月', "\n", '！'], $mb->toArray());

        $mb->set('本月，是五月。');
        static::assertSame(
            ['本月', '，', '是五月', '。', ''],
            $mb->toArraySplit('/([，。])/uS', -1, \PREG_SPLIT_DELIM_CAPTURE)
        );
    }
}
