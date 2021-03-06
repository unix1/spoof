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

namespace spoof\lib360\db\value;

/**
 * Database value interface
 */
interface IValue
{
    /**
     * Constructor creates an instance with specified type and value.
     *
     * Type must match the actual type of the value parameter.
     * This object will not explicitly cast the type.
     *
     * @param $value mixed value of the object
     * @param $type int type of the value
     *
     * @throws InvalidValueException when specified type and actual type do not match
     * @throws UnknownTypeException when invalid type is supplied
     */
    public function __construct($value, $type);

    /**
     * Returns the value type.
     *
     * @return integer value type
     */
    public function getType();

    /**
     * Returns value.
     *
     * @return mixed value
     */
    public function getValue();

}

?>
