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
 * Database model class
 */
abstract class Model implements IModel
{

    /**
     * Implementing classes must set this property via setTable method.
     *
     * This table object must
     * - be an instance of ITable
     * - have primary key set
     * - have fields set
     *
     * @var ITable object
     */
    protected static $table;

    /**
     * Internal storage of snapshot of data record
     * @var Record
     */
    protected $record;

    /**
     * Constructor sets child class table and validates it.
     *
     * @throws ModelException when the table object is not fully configured.
     */
    protected function __construct()
    {
        if (!isset(static::$table)) {
            // Set table instance to class
            $this->setTable();
            // Verify storage before moving on
            if (!(static::$table instanceof ITable)) {
                throw new ModelException('Model storage must be an instance of ITable.');
            }
            if (is_null(static::$table->key)) {
                throw new ModelException('Model storage has no key defined.');
            }
            if (count(static::$table->fields) == 0) {
                throw new ModelException('Model storage has no fields defined.');
            }
        }
    }

    /**
     * Creates new empty model instance.
     *
     * @return IModel instance of model implementation
     */
    public static function create()
    {
        $class = get_called_class();
        $model = new $class();
        $record = new Record();
        foreach (static::$table->fields as $field) {
            $record->$field = null;
        }
        $model->setRecord($record);
        return $model;
    }

    /**
     * Creates model from record.
     *
     * @param IRecord $record
     *
     * @return IModel instance of model implementation
     */
    public static function createFromRecord(IRecord $record)
    {
        $class = get_called_class();
        $model = new $class();
        $model->setRecord($record);
        return $model;
    }

    /**
     * Gets model by key.
     *
     * @param mixed $key
     *
     * @return IModel instance of model implementation
     */
    public static function getByKey($key)
    {
        $class = get_called_class();
        $model = new $class();
        $record = static::$table->selectRecord($key);
        $model->setRecord($record);
        return $model;
    }

    /**
     * Gets model list by attributes.
     *
     * @param array $attributes indexed by string keys and mixed values used for criteria
     *
     * @return ModelList list of model objects
     */
    public static function getByAttributes(array $attributes)
    {
        $class = get_called_class();
        // NOTE: makes sure constructor has run and child class table is set
        new $class();
        $recordlist = static::$table->selectRecords($attributes);
        return new ModelList($recordlist, $class);
    }

    /**
     * Gets attribute value.
     *
     * @param string $name Attribute name
     *
     * @return mixed Attribute value
     */
    public function get($name)
    {
        return $this->record->get($name);
    }

    /**
     * Returns whether the record has a value set for primary key.
     *
     * @return boolean
     */
    public function hasKey()
    {
        $set = false;
        if (isset($this->record[static::$table->key])) {
            $set = !is_null($this->record->get(static::$table->key));
        }
        return $set;
    }

    /**
     * Sets attribute value.
     *
     * @param string $name Attribute name
     * @param mixed $value Attribute value
     */
    public function set($name, $value)
    {
        $this->record->set($name, $value);
    }

    /**
     * Stores updated model attributes in the database.
     *
     * @return boolean true
     *
     * @throws RecordNotFoundException when record to update is not found
     */
    public function store()
    {
        if ($this->hasKey()) {
            static::$table->updateRecord($this->record);
        } else {
            static::$table->insertRecord($this->record);
        }
        return true;
    }

    /**
     * Deletes model from the database.
     *
     * @return boolean true
     *
     * @throws RecordNotFoundException when record to delete is not found
     */
    public function delete()
    {
        static::$table->deleteRecord($this->record);
        return true;
    }

    /**
     * Sets record object.
     *
     * This is useful when creating an instance of a model.
     *
     * @param IRecord $record Record object to set
     */
    protected function setRecord(IRecord $record)
    {
        $this->record = $record;
    }

}

?>