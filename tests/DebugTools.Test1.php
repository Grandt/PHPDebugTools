<?php
error_reporting(E_ALL | E_STRICT);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);

include "../vendor/autoload.php";

use grandt\DebugTools\DebugHelpers;
use grandt\DebugTools\HexBlock;

$srcFile = 'resources/Squares.gif';

echo "<!doctype html><html><head><title>DebugTools.Test1</title></head><body><pre>\n";
$fh = fopen($srcFile, "rb");
echo "Skip 3 bytes\n";
$data = fread($fh, 3);
echo "Dump 68 bytes\n";
echo HexBlock::createBlock($fh, 68, true);
echo "\n\nHexBlock::createBlock returns the file pointer to its original position:\n";
echo "Dump 0x10 bytes\n";
echo HexBlock::createBlock($fh, 0x10, true);
echo "\n\nRead 32 bytes into a string for use in the next calls.\n";
$data2 = fread($fh, 0x20);
fclose($fh);
echo "\nDump 16 bytes from a string (Will always start at pos 0x00. While the string was read from the same stream above, it doesn't come with an offset pointer into the stream.)\n";
echo HexBlock::createBlock($data2, 16, true);
echo "\n\nRequest Dump of 48 bytes from a string, 16 bytes more than is available.\n";
echo HexBlock::createBlock($data2, 48, true);
echo "\n\nAlternatively, turn the string into a file handle, and manipulate that.\n";
$fh = DebugHelpers::str2resource($data.$data2);
echo HexBlock::createBlock($fh, 32, true);
echo "Skip 3 bytes\n";
$data = fread($fh, 3);
echo "\n\nDump 68 bytes (using the data read earlier, 3 + 32 bytes total)\n";
echo HexBlock::createBlock($fh, 68, true);
fclose($fh);

echo "</pre></body></html>\n";
