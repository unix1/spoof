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

use spoof\lib360\db\condition\Condition;
use spoof\lib360\db\condition\ConditionGroup;
use spoof\lib360\db\condition\ICondition;
use spoof\lib360\db\connection\Pool;
use spoof\lib360\db\object\Factory;
use spoof\lib360\db\value\Value;

/**
 * A database table class.
 *
 * Provides functionality for simple operations against a database table.
 * This class can either be extended, or instantiated dynamically via TableFactory.
 * @see TableFactory
 */
class Table extends Store implements ITable
{
    /**
     * Optional view default fields array
     *
     * Extending classes may optionally (but recommended) define the list of
     * fields that will be returned by default. This default can be overriden
     * in the select method.
     */
    public $fields = array();

    /**
     * Table primary key.
     */
    public $key;

    /**
     * Gets table records by field criteria.
     *
     * @param array $conditions optional associative array of column names and
     *    their values to use as conditions, values will explicitly be cast as strings
     * @param array $fields optional array of fields to return, can be
     *    associative for (table field) => (select as field) or a simple array of
     *    table field names, will override default $fields property
     *
     * @return IRecordList object containing matched rows @see RecordList
     */
    public function selectRecords(array $conditions = array(), array $fields = null)
    {
        $condition_group = $this->getCondition($conditions);
        return $this->select($condition_group, array(), $fields);
    }

    /**
     * Gets table record by primary key.
     *
     * @param mixed $id primary key to retrieve the record
     * @param array $fields optional array of fields to return, can be
     *    associative for (table field) => (select as field) or a simple array of
     *    table field names, will override default $fields property
     *
     * @return Record object
     *
     * @throws RecordNotFoundException when no records found for given primary key
     * @throws RecordPrimaryKeyException when more than 1 record is found
     */
    public function selectRecord($id, array $fields = null)
    {
        $conditions = array($this->key => $id);
        $results = $this->selectRecords($conditions, $fields);
        if (count($results) == 0) {
            throw new RecordNotFoundException('Record not found.');
        }
        if (count($results) > 1) {
            throw new RecordPrimaryKeyException('More than 1 row found for primary key');
        }
        return $results[0];
    }

    /**
     * Gets table records that match the supplied database condition.
     *
     * @param ICondition $condition optional condition to apply to the query
     * @param array $values optional associative array of values for aliases
     *    in the condition object
     * @param array $fields optional array of fields to return, can be
     *    associative for (table field) => (select as field) or a simple array of
     *    table field names, will override default $fields property
     *
     * @return \spoof\lib360\db\data\IRecordList a database recordlist object
     * @see \spoof\lib360\db\data\RecordList
     *
     * @todo add order/group by support
     */
    public function select(
        ICondition $condition = null,
        array $values = array(),
        array $fields = null
    ) {
        // get connection object
        $db = Pool::getByName($this->db);
        // get language and query
        $return_fields = is_null($fields) ? $this->fields : $fields;
        $query = Factory::get(Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getSelect(
            $db->driver, $this,
            $condition, $return_fields
        );
        // get executor and execute
        $result = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->select(
            $db, $query->query,
            $query->values + $values, $this->name
        );
        // return result
        return $result;
    }

    /**
     * Updates database record(s) based on supplied criteria and values.
     *
     * @param array $fields associative array of fields to update
     *    (table field) => (update value)
     * @param ICondition $condition optional ICondition object to apply to the
     *    update
     * @param array $values optional associative array of values for aliases
     *    in the condition object
     *
     * @return integer number of rows updated
     */
    public function update(
        array $fields,
        ICondition $condition = null,
        array $values = array()
    ) {
        // get connection object
        $db = Pool::getByName($this->db);
        // get language and query
        $query = Factory::get(Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getUpdate(
            $db->driver, $this,
            $fields, $condition
        );
        // get executor and execute
        $result = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->update(
            $db, $query->query,
            $query->values + $values
        );
        // return result
        return $result;
    }

    /**
     * Stores record modifications in the table by record's primary key.
     *
     * @param IRecord $record
     *
     * @return int Number of rows updated
     *
     * @throws RecordNotFoundException when no rows are updated
     * @todo this function probably shouldn't throw an exception when an update
     *     results in 0 rows affected
     */
    public function updateRecord(IRecord $record)
    {
        $updated = 0;
        if ($record->isModified()) {
            $fields = array();
            foreach($record->getModified() as $key => $value) {
                $fields[$key] = new Value($value, Value::TYPE_STRING);
            }
            $condition = $this->getCondition(
                array($this->key => $record->getOriginal($this->key))
            );
            $updated = $this->update($fields, $condition);
            $record->clearModified();
        }
        if ($updated == 0) {
            throw new RecordNotFoundException('Record not found for updated');
        }
        return $updated;
    }

    /**
     * Inserts a database record.
     *
     * @param array $fields associative array of fields for insert
     *    (table field) => (insert value)
     *
     * @return mixed inserted row ID
     */
    public function insert(array $fields)
    {
        // get connection object
        $db = Pool::getByName($this->db);
        // get language and query
        $query = Factory::get(Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getInsert(
            $db->driver, $this,
            $fields
        );
        // get executor and execute
        $result = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->insert(
            $db, $query->query,
            $query->values
        );
        // return result
        return $result;
    }

    /**
     * Deletes table records that match the supplied database condition.
     *
     * @param ICondition $condition optional condition to apply to the query
     * @param array $values optional associative array of values for aliases
     *    in the condition object
     *
     * @return integer number of rows deleted
     */
    public function delete(ICondition $condition = null, array $values = array())
    {
        // get connection object
        $db = Pool::getByName($this->db);
        // get language and query
        $query = Factory::get(Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getDelete(
            $db->driver, $this,
            $condition
        );
        // get executor and execute
        $result = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->delete(
            $db, $query->query,
            $query->values + $values
        );
        // return result
        return $result;
    }

    protected function getCondition(array $conditions) {
        $condition_group = null;
        if (count($conditions) == 1) {
            foreach ($conditions as $column => $value) {
                $condition_group = new Condition(
                    new Value($column, Value::TYPE_COLUMN),
                    Condition::OPERATOR_EQUALS,
                    new Value((string)$value, Value::TYPE_STRING)
                );
            }
        } elseif (count($conditions) > 1) {
            foreach ($conditions as $column => $value) {
                $condition = new Condition(
                    new Value($column, Value::TYPE_COLUMN),
                    Condition::OPERATOR_EQUALS,
                    new Value((string)$value, Value::TYPE_STRING)
                );
                if (is_null($condition_group)) {
                    $condition_group = new ConditionGroup($condition);
                } else {
                    $condition_group->addCondition(ConditionGroup::OPERATOR_AND, $condition);
                }
            }
        }
        return $condition_group;
    }

}

?>
