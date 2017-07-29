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
 * A database record class
 *
 * This is a simple class that extends PHP ArrayObject and is used to
 * represent a database record.
 * @see \ArrayObject
 */
class Record extends \ArrayObject implements IRecord
{
    /**
     * Record type
     *
     * This is used when returning the output to differentiate the records for
     * the user environment.
     * @see toXML()
     */
    protected $__type;

    /**
     * Internal storage for modified keys and their associated original values
     * @var array
     */
    protected $__modified = array();

    /**
     * Constructor
     *
     * @param string $type optional type, default 'Record'
     */
    public function __construct($type = 'Record')
    {
        $this->__type = $type;
    }

    /**
     * Gets associated value of the supplied key.
     *
     * @param string $key
     *
     * @return mixed associated value
     *
     * @throws \OutOfBoundsException when key doesn't exist
     */
    public function __get($key)
    {
        if (!$this->offsetExists($key)) {
            throw new \OutOfBoundsException("Offset $key does not exist.");
        }
        return $this->offsetGet($key);
    }

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
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Gets associated value of the supplied key.
     *
     * @param string $key
     *
     * @return mixed associated value
     *
     * @throws \OutOfBoundsException when key doesn't exist
     */
    public function get($key)
    {
        return $this->__get($key);
    }

    /**
     * Gets modified fields and their updated values.
     *
     * @return array Associative array containing string keys and mixed
     *     associated original values
     */
    public function getModified()
    {
        $modified = array();
        foreach ($this->__modified as $key => $unused) {
            $modified[$key] = $this->__get($key);
        }
        return $modified;
    }

    /**
     * Gets original value associated with the key.
     *
     * @param string $key
     *
     * @return mixed associated original value
     */
    public function getOriginal($key)
    {
        return isset($this->__modified[$key]) ? $this->__modified[$key] : $this->get($key);
    }

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
    public function set($key, $value)
    {
        if (!$this->offsetExists($key)) {
            throw new \OutOfBoundsException("Offset $key does not exist.");
        }
        if (!isset($this->__modified[$key])) {
            $this->__modified[$key] = $this->__get($key);
        }
        $this->__set($key, $value);
    }

    /**
     * Returns whether the record was modified.
     *
     * @return boolean true if record was modified, false otherwise
     *
     * @see set
     */
    public function isModified()
    {
        return (count($this->__modified) > 0);
    }

    /**
     * Clears the modified fields and their original values.
     */
    public function clearModified()
    {
        $this->__modified = array();
    }

    /**
     * Clears all fields by setting their values to null.
     */
    public function clear()
    {
        $this->clearModified();
        foreach ($this as $key => $value) {
            $this->$key = null;
        }
    }

    /**
     * Transforms object into array representation.
     *
     * @return array associative array, field names as indexes
     */
    public function toArray()
    {
        return (array)$this;
    }

    /**
     * Transforms object into XML representation.
     *
     * @return \DOMDocument XML document object
     */
    public function toXML()
    {
        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $recordXML = $xml->appendChild($xml->createElement('record'));
        $recordXML->setAttribute('type', $this->__type);
        foreach ($this as $key => $value) {
            $recordXML->appendChild(
                $xml->createElement(
                    $key, htmlspecialchars(utf8_encode($value))
                )
            );
        }
        return $xml;
    }

}

?>
