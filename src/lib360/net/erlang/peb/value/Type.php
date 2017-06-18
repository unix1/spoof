<?php

/**
 *  This is Spoof.
 *  Copyright (C) 2011-2017  Spoof project.
 *
 *  Spoof is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Spoof is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Spoof.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace spoof\lib360\net\erlang\peb\value;

class Type
{
    /**
     * @todo separate primitive vs collection types somewhere
     */
    const ATOM = 1;
    const BINARY = 2;
    const DOUBLE = 3;
    const FLOAT = 4;
    const INTEGER = 5;
    const LONG = 6;
    const PID = 7;
    const STRING = 8;
    const UNSIGNED = 9;
    const LLIST = 10;
    const TUPLE = 11;

    public static $format = array(
        self::ATOM => '~a',
        self::BINARY => '~b',
        self::DOUBLE => '~d',
        self::FLOAT => '~f',
        self::INTEGER => '~i',
        self::LONG => '~l',
        self::PID => '~p',
        self::STRING => '~s',
        self::UNSIGNED => '~u',
        self::LLIST => array('start' => '[', 'end' => ']', 'separator' => ','),
        self::TUPLE => array('start' => '{', 'end' => '}', 'separator' => ','),
    );

}

?>
