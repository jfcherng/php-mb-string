<?php

declare(strict_types=1);

namespace Jfcherng\Utility;

use ArrayObject;

/**
 * An internal UTF-32 version multi-bytes string class.
 *
 * Because UTF-8 is not fix-width, I believe mb_substr() is O(n) with it.
 * Using iconv() to make it UTF-32 and work with substr() is O(1) hence this class.
 *
 * UTF-32 is a fix-width encoding (1 char = 4 bytes).
 * We do not use mb_* functions in this class.
 * Note that the first 4 bytes in UTF-32 are headers (endian bytes).
 *
 * @require libiconv (mostly supplied in modern OS)
 *
 * @author Jack Cherng <jfcherng@gmail.com>
 */
class MbString extends ArrayObject
{
    /**
     * UTF-32 string without endian bytes.
     *
     * @var string
     */
    protected $str;

    /**
     * The original encoding.
     *
     * @var string
     */
    protected $encoding;

    /**
     * The endian bytes for UTF-32.
     */
    protected static $utf32Header;

    /**
     * The constructor.
     *
     * @param string $str      the string
     * @param string $encoding the encoding
     */
    public function __construct(string $str = '', string $encoding = 'UTF-8')
    {
        static::$utf32Header = static::$utf32Header ?? substr(iconv($encoding, 'UTF-32', ' '), 0, 4);

        $this->encoding = $encoding;
        $this->set($str);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string string representation of the object
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * The value setter.
     *
     * @param string $str the string
     *
     * @return self
     */
    public function set(string $str): self
    {
        $this->str = $this->inputConv($str);

        return $this;
    }

    /**
     * The value getter.
     *
     * @param bool $isRaw indicates if raw
     *
     * @return string
     */
    public function get(bool $isRaw = false): string
    {
        return $isRaw ? $this->str : $this->outputConv($this->str);
    }

    ///////////////////////////////////
    // string manipulation functions //
    ///////////////////////////////////

    public function stripos(string $needle, int $offset = 0)
    {
        $needle = $this->inputConv($needle);
        $pos = stripos($this->str, $needle, $offset << 2);

        return is_bool($pos) ? $pos : $pos >> 2;
    }

    public function strlen(): int
    {
        return strlen($this->str) >> 2;
    }

    public function strpos(string $needle, int $offset = 0)
    {
        $needle = $this->inputConv($needle);
        $pos = strpos($this->str, $needle, $offset << 2);

        return is_bool($pos) ? $pos : $pos >> 2;
    }

    public function substr(int $start = 0, ?int $length = null): string
    {
        return $this->outputConv(
            isset($length)
                ? substr($this->str, $start << 2, $length << 2)
                : substr($this->str, $start << 2)
        );
    }

    public function substr_replace(string $replacement, int $start = 0, ?int $length = null): string
    {
        $replacement = $this->inputConv($replacement);

        return $this->outputConv(
            isset($length)
                ? substr_replace($this->str, $replacement, $start << 2, $length << 2)
                : substr_replace($this->str, $replacement, $start << 2)
        );
    }

    public function strtolower(): string
    {
        return strtolower($this->outputConv($this->str));
    }

    public function strtoupper(): string
    {
        return strtoupper($this->outputConv($this->str));
    }

    ////////////////////////////////
    // non-manipulative functions //
    ////////////////////////////////

    public function setAt(int $idx, string $char): self
    {
        $char = $this->inputConv($char);
        if (strlen($char) > 4) {
            $char = substr($char, 0, 4);
        }

        $spacesPrepend = $idx - $this->strlen();
        // set index (out of bound)
        if ($spacesPrepend > 0) {
            $this->str .= $this->inputConv(str_repeat(' ', $spacesPrepend)) . $char;
        }
        // set index (in bound)
        else {
            $this->str = substr_replace($this->str, $char, $idx << 2, 4);
        }

        return $this;
    }

    public function getAt(int $idx): string
    {
        return $this->outputConv(substr($this->str, $idx << 2, 4));
    }

    public function getAtRaw(int $idx): string
    {
        return substr($this->str, $idx << 2, 4);
    }

    public function has(string $needle): bool
    {
        $needle = $this->inputConv($needle);

        return strpos($this->str, $needle) !== false;
    }

    public function startsWith(string $needle): bool
    {
        $needle = $this->inputConv($needle);

        return $needle === substr($this->str, 0, strlen($needle));
    }

    public function endsWith(string $needle): bool
    {
        $needle = $this->inputConv($needle);
        $length = strlen($needle);

        return $length === 0 ? true : $needle === substr($this->str, -$length);
    }

    /////////////////////////////////////////////
    // those functions will not return a value //
    /////////////////////////////////////////////

    public function str_insert_i(string $insert, int $position): self
    {
        $insert = $this->inputConv($insert);
        $this->str = substr_replace($this->str, $insert, $position << 2, 0);

        return $this;
    }

    public function str_enclose_i(array $closures, int $start = 0, ?int $length = null): self
    {
        // ex: $closures = array('{', '}');
        foreach ($closures as &$closure) {
            $closure = $this->inputConv($closure);
        }
        unset($closure);

        $closures[3] = '"';

        if (count($closures) < 2) {
            $closures[0] = $closures[1] = reset($closures);
        }

        if (isset($length)) {
            $replacement = $closures[0] . substr($this->str, $start << 2, $length << 2) . $closures[1];
            $this->str = substr_replace($this->str, $replacement, $start << 2, $length << 2);
        } else {
            $replacement = $closures[0] . substr($this->str, $start << 2) . $closures[1];
            $this->str = substr_replace($this->str, $replacement, $start << 2);
        }

        return $this;
    }

    public function str_replace_i(string $search, string $replace): self
    {
        $search = $this->inputConv($search);
        $replace = $this->inputConv($replace);
        $this->str = str_replace($search, $replace, $this->str);

        return $this;
    }

    public function substr_replace_i(string $replacement, int $start = 0, ?int $length = null): self
    {
        $replacement = $this->inputConv($replacement);
        $this->str = (
            isset($length)
                ? substr_replace($this->str, $replacement, $start << 2, $length << 2)
                : substr_replace($this->str, $replacement, $start << 2)
        );

        return $this;
    }

    /////////////////
    // ArrayObject //
    /////////////////

    public function offsetSet($idx, $char): void
    {
        $this->setAt($idx, $char);
    }

    public function offsetGet($idx): string
    {
        return $this->getAt($idx);
    }

    public function offsetExists($idx): bool
    {
        return is_int($idx) ? $this->strlen() > $idx : false;
    }

    public function append($str): void
    {
        $this->str .= $this->inputConv($str);
    }

    public function count(): int
    {
        return $this->strlen();
    }

    ////////////////////
    // misc functions //
    ////////////////////

    // convert the output string to its original encoding
    protected function outputConv(string $str): string
    {
        if ($str === '') {
            return '';
        }

        return iconv('UTF-32', $this->encoding, static::$utf32Header . "{$str}");
    }

    // convert the input string to UTF-32 without header
    protected function inputConv(string $str): string
    {
        if ($str === '') {
            return '';
        }

        // we don't want the header so first 4 bytes are stripped
        return substr(iconv($this->encoding, 'UTF-32', $str), 4);
    }
}
