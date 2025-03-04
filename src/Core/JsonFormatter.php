<?php

declare(strict_types=1);

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Admin\Core;

use Exception;
use InvalidArgumentException;
use TypeError;

/**
 * Formats json strings used for php < 5.4 because the json_encode doesn't
 * supports the flags JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
 * in these versions
 *
 * @author Konstantin Kudryashiv <ever.zet@gmail.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class JsonFormatter
{
    /**
     * This code is based on the function found at:
     *  http://recursive-design.com/blog/2008/03/11/format-json-with-php/
     *
     * Originally licensed under MIT by Dave Perrett <mail@recursive-design.com>
     *
     *
     * @param  string  $json
     * @param  bool  $unescapeUnicode  Un escape unicode
     * @param  bool  $unescapeSlashes  Un escape slashes
     * @return string
     * @throws Exception
     */
    public static function format(string $json, bool $unescapeUnicode, bool $unescapeSlashes): string
    {
        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '    ';
        $newLine = "\n";
        $outOfQuotes = true;
        $buffer = '';
        $noescape = true;

        for ($i = 0; $i < $strLen; $i++) {
            // Grab the next character in the string
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ('"' === $char && $noescape) {
                $outOfQuotes = !$outOfQuotes;
            }

            if (!$outOfQuotes) {
                $buffer .= $char;
                $noescape = '\\' === $char ? !$noescape : true;
                continue;
            }
            if ('' !== $buffer) {
                if ($unescapeSlashes) {
                    $buffer = str_replace('\\/', '/', $buffer);
                }

                if ($unescapeUnicode && function_exists('mb_convert_encoding')) {
                    // https://stackoverflow.com/questions/2934563/how-to-decode-unicode-escape-sequences-like-u00ed-to-proper-utf-8-encoded-cha
                    $buffer = static::replaceCallback('/(\\\\+)u([0-9a-f]{4})/i', function ($match) {
                        $l = strlen($match[1]);

                        if ($l % 2) {
                            $code = hexdec($match[2]);
                            // 0xD800..0xDFFF denotes UTF-16 surrogate pair which won't be unescaped
                            // see https://github.com/composer/composer/issues/7510
                            if (0xD800 <= $code && 0xDFFF >= $code) {
                                return $match[0];
                            }

                            return str_repeat('\\', $l - 1).mb_convert_encoding(
                                    pack('H*', $match[2]),
                                    'UTF-8',
                                    'UCS-2BE'
                                );
                        }

                        return $match[0];
                    }, $buffer);
                }

                $result .= $buffer.$char;
                $buffer = '';
                continue;
            }

            if (':' === $char) {
                // Add a space after the : character
                $char .= ' ';
            } elseif ('}' === $char || ']' === $char) {
                $pos--;
                $prevChar = substr($json, $i - 1, 1);

                if ('{' !== $prevChar && '[' !== $prevChar) {
                    // If this character is the end of an element,
                    // output a new line and indent the next line
                    $result .= $newLine;
                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                } else {
                    // Collapse empty {} and []
                    $result = rtrim($result);
                }
            }

            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line
            if (',' === $char || '{' === $char || '[' === $char) {
                $result .= $newLine;

                if ('{' === $char || '[' === $char) {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
        }

        return $result;
    }

    /**
     * @param  string|string[]  $pattern
     * @param  callable  $replacement
     * @param  string  $subject
     * @param  int  $limit
     * @param  int|null  $count  Set by method
     * @param  int  $flags  PREG_OFFSET_CAPTURE is supported, PREG_UNMATCHED_AS_NULL is always set
     * @return string
     * @throws Exception
     */
    public static function replaceCallback(
        array|string $pattern,
        callable $replacement,
        mixed $subject,
        int $limit = -1,
        int &$count = null,
        int $flags = 0
    ): string {
        if (!is_scalar($subject)) {
            if (is_array($subject)) {
                throw new InvalidArgumentException('Array is not supported as subject');
            }

            throw new TypeError(sprintf('Is not scalar', gettype($subject)));
        }

        $result = preg_replace_callback($pattern, $replacement, $subject, $limit, $count,
            $flags | PREG_UNMATCHED_AS_NULL);
        if ($result === null) {
            throw new Exception('Error json build!');
        }

        return $result;
    }
}
