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

use spoof\lib360\db\condition\ICondition;

/**
 * Database model class
 */
interface IModel
{

    /**
     * Creates new empty model instance.
     *
     * @return IModel instance of model implementation
     */
    public static function create();

    /**
     * Creates model from record.
     *
     * @param IRecord $record
     *
     * @return IModel instance of model implementation
     */
    public static function createFromRecord(IRecord $record);

    /**
     * Gets model by key.
     *
     * @param mixed $key
     *
     * @return IModel instance of model implementation
     */
    public static function getByKey($key);

    /**
     * Gets model list by attributes.
     *
     * @param array $attributes indexed by string keys and mixed values used for criteria
     *
     * @return ModelList list of model objects
     */
    public static function getByAttributes(array $attributes);

    /**
     * Gets model list by condition.
     *
     * @param ICondition $condition
     *
     * @return ModelList list of model objects
     */
    public static function getByCondition(ICondition $condition);

    /**
     * Gets attribute value.
     *
     * @param string $name Attribute name
     *
     * @return mixed Attribute value
     */
    public function get($name);

    /**
     * Returns whether the record has a value set for primary key.
     *
     * @return boolean
     */
    public function hasKey();

    /**
     * Sets attribute value.
     *
     * @param string $name Attribute name
     * @param mixed $value Attribute value
     */
    public function set($name, $value);

//    public function setValues();

    /**
     * Stores updated model attributes in the database.
     *
     * @return boolean true
     */
    public function store();

    /**
     * Deletes model from the database.
     *
     * @return boolean
     */
    public function delete();

    /**
     * Sets implementation specific ITable object to static::$table property.
     */
    public function setTable();

    /**
     * Exports model to array representation.
     *
     * @return array associative array with field names as indexes
     */
    public function toArray();

}

?>
