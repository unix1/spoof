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

namespace spoof\lib360\crypt;

/**
 *    Random string generator
 */
class Random
{
    /**
     * Internal property to store values already generated; used to verify
     * uniqueness of a new value
     */
    protected static $randomValues = array();

    /**
     * Returns a random and optionally unique (per process) alphanumeric
     * string of a specified length.
     *
     * @param int $length length
     * @param boolean $unique whether value should be unique
     * @param boolean $addToUniqueList whether to add to unique list
     *
     * @return random string
     */
    public static function getString($length, $unique, $addToUniqueList)
    {
        $result = base_convert(mt_rand(base_convert(pow(10, $length - 1), 36, 10), pow(36, $length)), 10, 36);
        if ($unique) {
            while (isset(self::$randomValues[$result])) {
                $result = base_convert(mt_rand(base_convert(pow(10, $length - 1), 36, 10), pow(36, $length)), 10, 36);
            }
        }
        if ($addToUniqueList) {
            self::$randomValues[$result] = true;
        }
        return $result;
    }
}

?>
