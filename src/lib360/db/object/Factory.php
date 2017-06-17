<?php

namespace spoof\lib360\db\object;

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
 * This static class facilitates loading database objects.
 * TODO make this class more generic, not just for DB objects
 */
class Factory
{
    /**
     * Constant for the language object type
     */
    const OBJECT_TYPE_LANGUAGE = 'Language';

    /**
     * Constant for the driver object type
     */
    const OBJECT_TYPE_DRIVER = 'Driver';

    /**
     * Constant for the executor object type
     */
    const OBJECT_TYPE_EXECUTOR = 'Executor';

    /**
     * Base object types allowed.
     */
    protected static $types = array(
        'Executor' => array(
            'base' => '\spoof\lib360\db\executor\\',
            'interface' => '\spoof\lib360\db\executor\IExecutor'
        ),
        'Driver' => array('base' => '\spoof\lib360\db\driver\\', 'interface' => '\spoof\lib360\db\driver\IDriver'),
        'Language' => array(
            'base' => '\spoof\lib360\db\language\\',
            'interface' => '\spoof\lib360\db\language\ILanguage'
        )
    );

    /**
     * Internal array where objects are stored and cached.
     */
    protected static $objects = array();

    /**
     * Retrieves database object, maintains internal cache of loaded objects for future use.
     *
     * @param string $type type of object to retrieve
     * @param string $name name of object to retrieve
     *
     * @return mixed DB object requested
     *
     * @throw TypeNotFoundException when $type is invalid
     * @throw ClassNotFoundException when cannot find class definition for valid object type
     * @throw NotFoundException when object gets created but fails the check of implementing a pre-defined interface
     */
    public static function get($type, $name)
    {
        if (isset(self::$objects[$type]) && isset(self::$objects[$type][$name])) {
            $object = self::$objects[$type][$name];
        } else {
            if (!isset(self::$types[$type])) {
                throw new TypeNotFoundException("Undefined object type $type");
            }
            $class = self::$types[$type]['base'] . $name;
            if (!class_exists($class, true)) {
                throw new ClassNotFoundException("Couldn't find database class ($class) of type ($type).");
            }
            $object = new $class();
            if (!($object instanceof self::$types[$type]['interface'])) {
                throw new UnexpectedObjectTypeException("Database object of type ($class) is not an instance of " . self::$types[$type]['interface'] . ".");
            }
            self::$objects[$type][$name] = $object;
        }
        return $object;
    }

    /**
     * Sets object type configuration.
     *
     * @param string $name name of the object type
     * @param string $base namespace or similar prefix of object type
     * @param string $interface interface the object must implement
     */
    public static function setType($name, $base, $interface)
    {
        self::$types[$name] = array('base' => $base, 'interface' => $interface);
    }

    /**
     * Gets object type configuration
     * @param string $type type of object
     * @return array configuration
     * @throw \InvalidArgumentException when $type is not defined
     */
    public static function getType($type)
    {
        if (!isset(self::$types[$type])) {
            throw new \InvalidArgumentException("Unable to find object type definition for $type");
        }
        return self::$types[$type];
    }

    /**
     * Empties cache
     */
    public static function flushCache()
    {
        self::$objects = array();
    }

}

?>
