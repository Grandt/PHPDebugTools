<?php
error_reporting(E_ALL | E_STRICT);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);

include "../vendor/autoload.php";

use grandt\DebugTools\HexBlock;

$srcFile = 'resources/Squares.gif';

echo "<!doctype html><html><head><title>DebugTools.Test1</title></head><body><pre>\n";
$fh = fopen($srcFile, "rb");
echo "Skip 3 bytes\n";
fread($fh, 3);
echo "Dump 68 bytes\n";
echo HexBlock::createBlock($fh, 68, true);
echo "\n\nHexBlock::createBlock returns the file pointer to its original position:\n";
echo "Dump 0x10 bytes\n";
echo HexBlock::createBlock($fh, 0x10, true);
fclose($fh);
echo "</pre></body></html>\n";
