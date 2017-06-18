<?php

namespace spoof\lib360\db\value;

/**
 *  This is Spoof.
 *  Copyright (C) 2011-2012  Spoof project.
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

/**
 * A database value class implementation
 */
class Value implements IValue
{
    /**
     * Database value type null
     */
    const TYPE_NULL = -1;

    /**
     * Database value type string
     */
    const TYPE_STRING = 1;

    /**
     * Database value type integer
     */
    const TYPE_INTEGER = 2;

    /**
     * Database value type float
     */
    const TYPE_FLOAT = 3;

    /**
     * Database value type boolean
     */
    const TYPE_BOOLEAN = 4;

    /**
     * Database value type binary
     */
    const TYPE_BINARY = 5;

    /**
     * Database value type array
     */
    const TYPE_ARRAY = 6;

    /**
     * Database value type column
     */
    const TYPE_COLUMN = 7;

    /**
     * Database value type prepared
     */
    const TYPE_PREPARED = 8;

    /**
     * Database value type function
     */
    const TYPE_FUNCTION = 9;

    /**
     * Protected value type property
     */
    protected $type;

    /**
     * Protected value property
     */
    protected $value;

    /**
     * Constructor creates an instance with specified type and value.
     *
     * Type must match the actual type of the value parameter.
     * This object will not explicitly cast the type.
     *
     * @param $value mixed value of the object
     * @param $type int type of the value
     *
     * @throw InvalidValueException when specified type and actual type do not match
     * @throw UnknownTypeException when invalid type is supplied
     */
    public function __construct($value, $type)
    {
        // validate type and value
        /// NOTE use of gettype is not recommended in PHP to determine types. We use it only to provide information in exceptions.
        switch ($type) {
            case self::TYPE_NULL:
                if (!is_null($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). NULL was expected."
                    );
                }
                break;
            case self::TYPE_STRING:
                if (!is_string($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). String was expected."
                    );
                }
                break;
            case self::TYPE_INTEGER:
                if (!is_int($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). Integer was expected."
                    );
                }
                break;
            case self::TYPE_FLOAT:
                if (!is_float($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). Float was expected."
                    );
                }
                break;
            case self::TYPE_BOOLEAN:
                if (!is_bool($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). Boolean was expected."
                    );
                }
                break;
            case self::TYPE_BINARY:
                /// NOTE this is only valid for PHP >= 6
                //if (!is_binary($value))
                //{
                //	throw new InvalidValueExceptionv("Value argument has invalid type (" . gettype($value) . "). Binary was expected.");
                //}
                break;
            case self::TYPE_ARRAY:
                if (!is_array($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). Array was expected."
                    );
                }
                break;
            case self::TYPE_COLUMN:
                if (!is_string($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). String column name was expected."
                    );
                }
                break;
            case self::TYPE_PREPARED:
                if (!is_string($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). String prepared label was expected."
                    );
                }
                break;
            case self::TYPE_FUNCTION:
                if (!is_array($value)) {
                    throw new InvalidValueException(
                        "Value argument has invalid type (" . gettype($value) . "). Array was expected."
                    );
                }
                break;
            default:
                throw new UnknownTypeException("Invalid or unsupported value type ($type).");
        }
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Returns the value type.
     *
     * @return integer value type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns value.
     *
     * @return mixed value
     */
    public function getValue()
    {
        return $this->value;
    }

}

?>
