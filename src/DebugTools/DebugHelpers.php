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

use com\grandt\BinStringStatic;

class DebugHelpers {

    /**
     * Turn a string into a in-memory file resource.
     * Remember to close it when done to free up the used memory.
     *
     * @param string $data
     *
     * @return resource the in-memory file handle.
     */
    public static function str2resource($data) {
        $length = BinStringStatic::_strlen($data);
        $handle = fopen("php://temp/maxmemory:$length", 'r+');
        fputs($handle, $data);
        rewind($handle);

        return $handle;
    }
}
