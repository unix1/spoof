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

namespace spoof\lib360\db\data;

/**
 * Database record interface.
 * This interface is used to define database data record.
 */
interface IRecord
{
    /**
     * Constructor
     *
     * @param string $type optional type, default 'Record'
     */
    public function __construct($type = 'Record');

    /**
     * Gets associated value of the supplied key.
     *
     * @param string $key
     *
     * @return mixed associated value
     *
     * @throws \OutOfBoundsException when key doesn't exist
     */
    public function __get($key);

    /**
     * Sets key-value association.
     *
     * Creates new key if necessary, doesn't track the modification and doesn't
     * store the original value. This is primarily useful for setting original
     * values from data source, or other default values.
     *
     * @param string $key
     * @param mixed $value
     *
     * @see set
     */
    public function __set($key, $value);

    /**
     * Gets associated value of the supplied key.
     *
     * @param string $key
     *
     * @return mixed associated value
     *
     * @throws \OutOfBoundsException when key doesn't exist
     */
    public function get($key);

    /**
     * Gets modified fields and their original values.
     *
     * @return array Associative array containing string keys and mixed
     *     associated original values
     */
    public function getModified();

    /**
     * Gets original value associated with the key.
     *
     * @param string $key
     *
     * @return mixed associated original value
     */
    public function getOriginal($key);

    /**
     * Sets key-value association.
     *
     * The difference between this and @see __set is that this function tracks
     * the modification, stores the original value, and doesn't create new
     * keys.
     *
     * @param $key
     * @param $value
     *
     * @throws \OutOfBoundsException when key doesn't exist
     *
     * @see __set
     */
    public function set($key, $value);

    /**
     * Returns whether the record was modified.
     *
     * @return bool true if record was modified, false otherwise
     *
     * @see set
     */
    public function isModified();

    /**
     * Clears the modified fields and their original values.
     */
    public function clearModified();

    /**
     * Transforms object into XML representation.
     *
     * @return \DOMDocument XML document object
     */
    public function toXML();

}

?>
