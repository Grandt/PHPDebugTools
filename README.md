# DebugTools


This package aims to provide some nice debug tools. Well, just one tool for now.
 

## Introduction

For now, only the HexBlock is added. What it does is provide an easy way to dump binary data directly from a currently open file stream.
Example output of a gif file, where the file pointer is on position 3, the method have been told to dump the following 68 bytes.

Note that the file pointer will be reset to its initial position after the dump, essentially leaving the handle unaffected.
```
    Start: 0x03; Length: 68 (0x44)
    00: -- -- -- 38 39 61 59 00  68 00 c4 15 00 ad df ff | ---89aY. h.......
    10: e5 f4 ff cb ea ff ff ff  ff 98 d6 fe b7 b9 bb 81 | ........ ........
    20: d2 ff 77 cc ff 8f c5 fe  54 9a f5 11 28 7d 65 ab | ..w..... T...(}e.
    30: fe 52 75 a1 7a b7 fb 66  cc ff 5b cc fe 00 66 ff | .Ru.z..f ..[...f.
    40: 0f 71 ff ff 39 00 b3 --  -- -- -- -- -- -- -- -- | .q..9..- --------
```

## Usage

Call 
```php
    $block = HexBlock::createBlock($handle, $length, $encodeHtml [Default = true]);
```

$encodeHtml merely ensures that any html entities are encoded as such, but doesn't add any line break tags.

### Import

Add this requirement to your `composer.json` file:
```json
    "grandt/phpdebugtools": ">=1.0.0"
```

### Composer
If you already have Composer installed, skip this part.

[Packagist](https://packagist.org/), the main composer repository has a neat and very short guide.

Or you can look at the guide at the [Composer site](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).
 
The easiest for first time users, is to have the composer installed in the same directory as your composer.json file, though there are better options.

Run this from the command line:
```
php -r "readfile('https://getcomposer.org/installer');" | php
```

This will check your PHP installation, and download the `composer.phar`, which is the composer binary. This file is not needed on the server though.

Once composer is installed you can create the `composer.json` file to import this package.
```json
{
    "require": {
        "grandt/phpdebugtools": ">=1.0.0"
    }
}
```

Followed by telling Composer to install the dependencies.
```
php composer.phar install
```

this will download and place all dependencies defined in your `composer.json` file in the `vendor` directory.

Finally, you include the `autoload.php` file in the new `vendor` directory.
```php
<?php
    require 'vendor/autoload.php';
    .
    .
    .
```

### Example
```php
include "../vendor/autoload.php";
use grandt\DebugTools;

$srcFile = "[path to file]";

$fh = fopen($srcFile, "rb");
echo HexBlock::createBlock($fh, 68, true);
fclose($fh);
```
