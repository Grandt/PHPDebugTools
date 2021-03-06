<?php
/**
 * Copyright (C) 2015  A. Grandt
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015- A. Grandt
 * @license   GNU LGPL 2.1
 */
namespace grandt\DebugTools;

/**
 * Create a hex block from the current position in the file.
 *
 * A Hex dump produces for example this output for the first 64 bytes of a gif file:
 *
 * Start: 0x00; Length: 64 (0x40)
 * 00: 47 49 46 38 39 61 59 00  68 00 c4 15 00 ad df ff | GIF89aY. h.......
 * 10: e5 f4 ff cb ea ff ff ff  ff 98 d6 fe b7 b9 bb 81 | ........ ........
 * 20: d2 ff 77 cc ff 8f c5 fe  54 9a f5 11 28 7d 65 ab | ..w..... T...(}e.
 * 30: fe 52 75 a1 7a b7 fb 66  cc ff 5b cc fe 00 66 ff | .Ru.z..f ..[...f.
 */
class HexBlock {

    /**
     * Print a HexBlock from the current position in the file stream.
     *
     * The pointer will be returned to the initial position when done, essentially leaving the file stream untouched.
     *
     * @param resource $handle
     * @param int      $bytes      number of bytes to print.
     * @param bool     $encodeHTML Encode the special characters that may occur on the right panel.
     */
    public static function printBlock($handle, $bytes, $encodeHTML = true) {
        echo self::createBlock($handle, $bytes, $encodeHTML);
    }

    /**
     * Generate a HexBlock from the current position in the file stream.
     *
     * The pointer will be returned to the initial position when done, essentially leaving the file stream untouched.
     *
     * @param resource|string $data       a resource handle or a string.
     * @param int             $bytes      number of bytes to print. -1 will dump the entirety of the file.
     * @param bool            $encodeHTML Encode the special characters that may occur on the right panel.
     *
     * @return string  The generated Hex Block.
     */
    public static function createBlock($data, $bytes, $encodeHTML = true) {
        $out = "";
        /** @var resource $handle */
        $handle = null;
        $isLocalHandle = false;

        if (is_string($data)) {
            $handle = DebugHelpers::str2resource($data);
            $isLocalHandle = true;
        } else if (is_resource($data)) {
            $handle = $data;
        } else {
            return $out;
        }

        $stat = fstat($handle);
        $length = $stat['size'];
        $pos = ftell($handle);

        if ($bytes === -1) {
            $bytes = $length-$pos;
        }

        $endPos = $pos + $bytes;
        $realPos = $pos & 0xfffffff0;
        fseek($handle, $realPos, SEEK_SET);

        $isRangeOverreached = ($pos + $bytes) > $length;

        $rDigits = strlen(dechex($isRangeOverreached ? $length : $pos + $bytes));
        $rDigits += $rDigits%2;
        if ($rDigits < 2) {
            $rDigits = 2;
        }

        $lDigits = strlen(dechex($bytes));
        $lDigits += $lDigits%2;
        if ($lDigits < 2) {
            $lDigits = 2;
        }

        $out .= "Start: 0x" . str_pad(strtoupper(dechex($pos)), $rDigits, "0", STR_PAD_LEFT);
        $out .= "; Length: " . $bytes . " (0x" . str_pad(strtoupper(dechex($bytes)), $lDigits, "0", STR_PAD_LEFT) . ")\n";
        if ($isRangeOverreached) {
            $or = $pos + $bytes - $length;
            $out .= "* Requested length overreach by " . $or . " bytes, only " . ($bytes - $or) . " available in stream.\n";
            if ($bytes - $or <= 0) {
                fseek($handle, $pos, SEEK_SET);

                return $out;
            }
        }

        while (ftell($handle) < $endPos && !(feof($handle) || ftell($handle) === $length)) {
            $lsp = ftell($handle);

            $out .= str_pad(strtoupper(dechex($lsp)), $rDigits, "0", STR_PAD_LEFT) . ": ";
            $rp = "| ";

            for ($j = 0; $j < 16; $j++) {
                if ($j == 8) {
                    $rp .= " ";
                    $out .= " ";
                }
                if (($lsp + $j) < $pos) {
                    $rp .= "-";
                    $out .= "-- ";
                    fread($handle, 1);
                } elseif (($lsp + $j) >= $endPos || (feof($handle) || ftell($handle) === $length)) {
                    $rp .= "-";
                    $out .= "-- ";
                } else {
                    $d = fread($handle, 1);
                    $out .= bin2hex($d) . " ";

                    $od = ord($d);
                    if ($od < 0x20 || $od >= 0x7f) {
                        $d = '.';
                    }
                    $rp .= $encodeHTML ? htmlspecialchars($d) : $d;
                }
            }

            $out .= $rp . "\n";
        }
        $out .= "\n";

        if ($isLocalHandle) {
            fclose($handle);
        } else {
            fseek($handle, $pos, SEEK_SET);
        }

        return $out;
    }
}
