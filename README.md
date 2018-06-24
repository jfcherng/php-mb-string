# php-mb-string [![Build Status](https://travis-ci.org/jfcherng/php-mb-string.svg?branch=master)](https://travis-ci.org/jfcherng/php-mb-string)

An implementation targeting high performance for frequently reading/writing operations.


# Why

Consider that you have a long multibyte string and you want to do lots of following operations on it.

- Random reading/writing such as `$str[5];` or `$str[5] = '許';`.
- Replacement such as `str_replace($search, $replace, $str);`.
- Insertion such as `substr_replace($insert, $str, $position, 0);`.
- Get substring such as `substr($str, $start, $length);`.

Because the string in PHP is not UTF-8 by default, to do operations above,
you have to either use `mb_*()` functions or calculate the index by yourself.
Using `mb_*()` functions frequently could be a performance loss because it has
to do string encode/decode every time.

Instead, this class internally stores the string in a UTF-32 form.
UTF-32 is fixed-width (while UTF-8 is vary-width) so we are able to perform a
random access with speed. With the power of random access, we could use
`str_*()` functions to do the job internally.


# Installation

```
$ composer require jfcherng/php-mb-string --no-dev
```


# Example

See [tests/MbStringTest.php](https://github.com/jfcherng/php-mb-string/blob/master/tests/MbStringTest.php).


Supporters <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ATXYY9Y78EQ3Y" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" /></a>
==========

Thank you guys for sending me some cups of coffee.
