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

class ModelList implements IModelList
{

    /**
     * List of records to iterate over
     * @var IRecordList
     */
    protected $recordList;

    /**
     * Model subclass, used to instantiate model object during iteration
     * @var string
     */
    protected $modelClass;

    /**
     * Constructor, sets record list.
     *
     * @param IRecordList $recordList Record list to iterate over
     * @param string $modelClass Model class used for instantiating model objects during iteration
     */
    public function __construct(IRecordList $recordList, $modelClass)
    {
        $this->recordList = $recordList;
        $this->modelClass = $modelClass;
    }

    /**
     * Exports model list to array representation.
     *
     * @return array array of associative arrays with field names as indexes
     */
    public function toArray()
    {
        $models = array();
        foreach ($this as $model) {
            $models[] = $model->toArray();
        }
        return $models;
    }

    /**
     *  Gets a count of elements in a list.
     *
     * @return int Number of elements
     */
    public function count()
    {
        return count($this->recordList);
    }

    /**
     * Gets current model object.
     *
     * @return IModel instance of Model implementation
     */
    public function current()
    {
        $record = current($this->recordList);
        $class = $this->modelClass;
        return $class::createFromRecord($record);
    }

    /**
     * Gets current key.
     *
     * @return int|null|string
     */
    public function key()
    {
        return key($this->recordList);
    }

    /**
     * Advances internal pointer to next element, return its model object.
     *
     * @return IModel instance of Model implementation
     */
    public function next()
    {
        $model = false;
        $record = next($this->recordList);
        if ($record !== false) {
            $class = $this->modelClass;
            $model = $class::createFromRecord($record);
        }
        return $model;
    }

    /**
     * Resets internal pointer to first element, return its model object.
     *
     * @return IModel|boolean instance of Model implementation or boolean false when array is empty
     */
    public function rewind()
    {
        $value = false;
        $record = reset($this->recordList);
        if ($record !== FALSE) {
            $class = $this->modelClass;
            $value = $class::createFromRecord($record);
        }
        return $value;
    }

    /**
     * Returns whether iterator has reached end of the list.
     *
     * @return boolean whether current pointer position is valid
     */
    public function valid()
    {
        return key($this->recordList) !== null;
    }

    /**
     * Returns whether an offset exists.
     *
     * @param mixed $offset The offset to check
     *
     * @return boolean Whether an offset exists
     */
    public function offsetExists($offset)
    {
        return $this->recordList->offsetExists($offset);
    }

    /**
     * Returns the value at the specified offset.
     *
     * @param mixed $offset The offset to retrieve
     *
     * @return mixed Value at specified offset
     */
    public function offsetGet($offset)
    {
        $record = $this->recordList->offsetGet($offset);
        $class = $this->modelClass;
        return $class::createFromRecord($record);
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value The value to set
     */
    public function offsetSet($offset, $value)
    {
        $this->recordList->offsetSet($offset, $value);
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset The offset to unset
     */
    public function offsetUnset($offset)
    {
        $this->recordList->offsetUnset($offset);
    }

}

?>
