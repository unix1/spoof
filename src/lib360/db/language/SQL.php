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

namespace spoof\lib360\db\language;

use spoof\lib360\crypt\Random;
use spoof\lib360\db\condition\Condition;
use spoof\lib360\db\condition\ConditionGroup;
use spoof\lib360\db\condition\ICondition;
use spoof\lib360\db\condition\IConditionGroup;
use spoof\lib360\db\data\IDataStore;
use spoof\lib360\db\data\IStore;
use spoof\lib360\db\data\ITable;
use spoof\lib360\db\data\IView;
use spoof\lib360\db\data\Table;
use spoof\lib360\db\driver\IDriver;
use spoof\lib360\db\join\IJoin;
use spoof\lib360\db\join\Join;
use spoof\lib360\db\join\UnknownTypeException;
use spoof\lib360\db\query\Query;
use spoof\lib360\db\value\IValue;
use spoof\lib360\db\value\Value;

/**
 * Database language implementation for SQL
 */
class SQL implements ILanguage
{
    const SELECT = 'select';
    const SELECT_AS = 'as';
    const SELECT_FIELD_SEPARATOR = ', ';
    const SELECT_FIELDS_ALL = '*';
    const SELECT_JOIN_ON = 'on';
    const SELECT_JOIN_SEPARATOR = ', ';
    const UPDATE = 'update';
    const UPDATE_SET = 'set';
    const UPDATE_FIELD_SEPARATOR = ', ';
    const DELETE = 'delete';
    const INSERT = 'insert';
    const INSERT_INTO = 'into';
    const INSERT_VALUES = 'values';
    const INSERT_FIELD_SEPARATOR = ', ';
    const INSERT_VALUES_WRAPPER_START = '(';
    const INSERT_VALUES_WRAPPER_END = ')';
    const JOIN_TYPE_LEFT_OUTER = 'left outer join';
    const JOIN_TYPE_INNER = 'inner join';
    const JOIN_TYPE_JOIN = 'join';
    const JOIN_TYPE_RIGHT_OUTER = 'right outer join';
    const JOIN_TYPE_FULL = 'full join';
    const FROM = 'from';
    const WHERE = 'where';
    const EQUALS = '=';
    const CONDITION_WRAPPER_START = '(';
    const CONDITION_WRAPPER_END = ')';
    const CONDITION_VALUES_WRAPPER_START = '(';
    const CONDITION_VALUES_WRAPPER_END = ')';
    const CONDITION_VALUES_SEPARATOR = ', ';
    const CONDITION_EQUALS = '=';
    const CONDITION_NOT_EQUALS = '<>';
    const CONDITION_EQUALS_NULL = 'is';
    const CONDITION_NOT_EQUALS_NULL = 'is not';
    const CONDITION_GREATER_THAN = '>';
    const CONDITION_GREATER_THAN_OR_EQUAL = '>=';
    const CONDITION_LESS_THAN = '<';
    const CONDITION_LESS_THAN_OR_EQUAL = '<=';
    const CONDITION_IN = 'in';
    const CONDITION_NOT_IN = 'not in';
    const CONDITIONGROUP_AND = 'and';
    const CONDITIONGROUP_OR = 'or';
    const VALUE_NULL = 'null';
    const BIND_CHAR = ':';

    /**
     * Returns query object for full select statement.
     *
     * @param IDriver $driver database driver
     * @param IStore $storage database storage object
     * @param ICondition $condition optional database condition object, default NULL
     * @param array $fields optional (optionally associative) array of fields to query and return, default NULL
     *
     * @return Query database query object
     */
    public function getSelect(IDriver $driver, IStore $storage, ICondition $condition = null, array $fields = null)
    {
        $query = new Query(self::SELECT . ' ' . $this->getSelectFields($driver, $fields) . ' ' . self::FROM);
        $query->addQuery($this->getSelectFrom($driver, $storage));
        if (!is_null($condition)) {
            $query->addString(self::WHERE);
            $query->addQuery($this->getCondition($driver, $condition));
        }
        return $query;
    }

    /**
     * Returns formatted list of fields for select statement.
     *
     * @param IDriver $driver database driver
     * @param array $fields optional (optionally associative) array of fields to format
     *
     * @return string formatted field list
     */
    public function getSelectFields(IDriver $driver, array $fields = null)
    {
        if (!is_null($fields) && is_array($fields) && count($fields) > 0) {
            $fieldsSelect = array();
            foreach ($fields as $fieldKey => $fieldValue) {
                $fieldsSelect[] = $this->getSelectFieldFormatted($driver, $fieldKey, $fieldValue);
            }
            $fieldsString = implode(self::SELECT_FIELD_SEPARATOR, $fieldsSelect);
        } else {
            $fieldsString = self::SELECT_FIELDS_ALL;
        }
        return $fieldsString;
    }

    /**
     * Returns formatted field for select statement.
     *
     * If associative array given the result will be "key AS value".
     * If non-associative array given element value will be used as the field name.
     *
     * @param IDriver $driver database driver
     * @param mixed $fieldKey can be associative string key or integer
     * @param string $fieldValue field name
     *
     * @return string formatted field
     */
    public function getSelectFieldFormatted(IDriver $driver, $fieldKey, $fieldValue)
    {
        if (is_numeric($fieldKey)) {
            $field = $this->getFieldFormatted($driver, $fieldValue);
        } else {
            $field = $this->getFieldFormatted(
                    $driver,
                    $fieldKey
                ) . ' ' . self::SELECT_AS . ' ' . $driver->columnQuoteStart . $fieldValue . $driver->columnQuoteEnd;
        }
        return $field;
    }

    /**
     * Returns formatted SQL field string.
     *
     * @param IDriver $driver database driver
     * @param string $field unformatted raw field
     *
     * @return string formatted field
     */
    public function getFieldFormatted(IDriver $driver, $field)
    {
        return $driver->columnQuoteStart . str_replace(
                $driver->columnSeparator,
                $driver->columnQuoteEnd . $driver->columnSeparator . $driver->columnQuoteStart,
                $field
            ) . $driver->columnQuoteEnd;
    }

    /**
     * Returns the "from" section of the SQL select query for the given storage object.
     *
     * @param IDriver $driver database driver
     * @param IStore $storage IView or ITable object for which the result will be generated
     *
     * @return Query object
     *
     * @throw \InvalidArgumentException when invalid or unsupported storage object type is given
     */
    public function getSelectFrom(IDriver $driver, IStore $storage)
    {
        if ($storage instanceof IView) {
            $query = $this->getSelectFromView($driver, $storage);
        } elseif ($storage instanceof ITable) {
            $query = new Query($this->getSelectFromTable($driver, $storage));
        } else {
            throw new \InvalidArgumentException(
                "Storage object of type \spoof\lib360\db\data\IView and \spoof\lib360\db\data\ITable expected, " . get_class(
                    $storage
                ) . " given."
            );
        }
        return $query;
    }

    /**
     * Returns query object for the "from" portion of the select query for a view.
     *
     * @param IDriver $driver database driver
     * @param IView $storage database view object
     *
     * @return Query object
     *
     * @throw \InvalidArgumentException when any of the view joins are not \spoof\lib360\db\join\IJoin, ITable, or string table name
     *
     * @see \spoof\lib360\db\data\View
     */
    public function getSelectFromView(IDriver $driver, IView $storage)
    {
        $query = new Query();
        $i = 0;
        foreach ($storage->joins as $key => $join) {
            if ($i > 0) {
                $query->addString(self::SELECT_JOIN_SEPARATOR, false);
            }
            if ($join instanceof Join) {
                $query->addQuery($this->getJoin($driver, $join));
            } elseif ($join instanceof Table) {
                $query->addString($this->getSelectFromTable($driver, $join));
            } elseif (is_string($join)) {
                $query->addString($this->getSelectFromTableName($driver, $join));
            } else {
                throw new \InvalidArgumentException(
                    "View object join element at index $key must be an instance of \spoof\lib360\db\join\IJoin, \spoof\lib360\db\data\ITable, or string"
                );
            }
            $i++;
        }
        return $query;
    }

    /**
     * Generates a query object for a database join.
     *
     * @param IDriver $driver database driver
     * @param IJoin $join database join
     *
     * @return Query object
     */
    public function getJoin(IDriver $driver, IJoin $join)
    {
        $query = new Query();
        $query->setString($driver->tableQuoteStart . $join->tableBase . $driver->tableQuoteEnd);
        foreach ($join->tableJoin as $i => $table) {
            switch ($join->type[$i]) {
                case $join::JOIN_TYPE_LEFT_OUTER:
                    $joinString = self::JOIN_TYPE_LEFT_OUTER;
                    break;
                case $join::JOIN_TYPE_INNER:
                    $joinString = self::JOIN_TYPE_INNER;
                    break;
                case $join::JOIN_TYPE_JOIN:
                    $joinString = self::JOIN_TYPE_JOIN;
                    break;
                case $join::JOIN_TYPE_RIGHT_OUTER:
                    $joinString = self::JOIN_TYPE_RIGHT_OUTER;
                    break;
                case $join::JOIN_TYPE_FULL:
                    $joinString = self::JOIN_TYPE_FULL;
                    break;
                default:
                    throw new UnknownTypeException("Unsupported join type " . $join->type[$i]);
            }
            $query->addString($joinString);
            $query->addString($driver->tableQuoteStart . $table . $driver->tableQuoteEnd);
            $query->addString(self::SELECT_JOIN_ON);
            $query->addQuery($this->getCondition($driver, $join->condition[$i]));
        }
        return $query;
    }

    /**
     * Gets query of the condition using driver-specific syntax.
     *
     * @param IDriver $driver database driver
     * @param ICondition $condition database condition object
     *
     * @return Query database query object
     */
    public function getCondition(IDriver $driver, ICondition $condition)
    {
        $query = new Query();
        if ($condition instanceof IConditionGroup) {
            $query->setString(self::CONDITION_WRAPPER_START);
            $query->addQuery($this->getCondition($driver, $condition->condition));
            foreach ($condition->conditions as $i => $cond) {
                $query->addString($this->getConditionGroupOperator($driver, $condition->operators[$i]));
                $query->addQuery($this->getCondition($driver, $cond));
            }
            $query->addString(self::CONDITION_WRAPPER_END);
        } elseif ($condition instanceof ICondition) {
            $query->addQuery($this->getValue($driver, $condition->value1));
            $query->addString($this->getConditionOperator($driver, $condition));
            $query->addQuery($this->getValue($driver, $condition->value2));
        }
        return $query;
    }

    /**
     * Returns SQL operator for given condition group object operator.
     *
     * @param IDriver $driver database driver object
     * @param integer $groupOperator one of ConditionGroup operator constants' values
     *
     * @return string SQL operator
     *
     * @throw SQLException when illegal or unsupported operator given
     *
     * @see ConditionGroup
     */
    public function getConditionGroupOperator(IDriver $driver, $groupOperator)
    {
        switch ($groupOperator) {
            case ConditionGroup::OPERATOR_AND:
                $operator = self::CONDITIONGROUP_AND;
                break;
            case ConditionGroup::OPERATOR_OR:
                $operator = self::CONDITIONGROUP_OR;
                break;
            default:
                throw new SQLException("Unsupported or illegal condition group operator (" . $groupOperator . ").");
        }
        return $operator;
    }

    /**
     * Returns Query object for given IValue.
     *
     * @param IDriver $driver database driver object
     * @param IValue $value database value object
     *
     * @return Query object
     */
    public function getValue(IDriver $driver, IValue $value)
    {
        $query = new Query();
        switch ($value->getType()) {
            case Value::TYPE_PREPARED:
                $query->addString(self::BIND_CHAR . $value->getValue());
                break;

            case Value::TYPE_COLUMN:
                $query->addString($this->getFieldFormatted($driver, $value->getValue()));
                break;

            case Value::TYPE_NULL:
                $query->addString(self::VALUE_NULL);
                break;

            case Value::TYPE_ARRAY:
                $query->addString(self::CONDITION_VALUES_WRAPPER_START);
                $firstValue = true;
                foreach ($value->getValue() as $v) {
                    if (!$firstValue) {
                        $query->addString(self::CONDITION_VALUES_SEPARATOR, false);
                    }
                    $query->addQuery($this->getValue($driver, $v));
                    $firstValue = false;
                }
                $query->addString(self::CONDITION_VALUES_WRAPPER_END);
                break;

            /// @todo implement function type
            //case \lib360\db\value\Value::TYPE_FUNCTION:
            //break;

            case Value::TYPE_STRING:
            case Value::TYPE_INTEGER:
            case Value::TYPE_FLOAT:
            case Value::TYPE_BOOLEAN:
            case Value::TYPE_BINARY:
            default:
                $tag = (string)Random::getString(4, true, true);
                $query->addString(self::BIND_CHAR . $tag);
                $query->values[$tag] = $value;
                break;
        }
        return $query;
    }

    /**
     * Returns condition operator for the given ICondition object.
     *
     * @param IDriver $driver database driver object
     * @param ICondition $condition database condition object
     *
     * @return string SQL operator
     *
     * @throw SQLException when condition operator is invalid or unsupported
     */
    public function getConditionOperator(IDriver $driver, ICondition $condition)
    {
        switch ($condition->operator) {
            case Condition::OPERATOR_EQUALS:
                if ($condition->value2->getType() == Value::TYPE_NULL) {
                    $operator = self::CONDITION_EQUALS_NULL;
                } else {
                    $operator = self::CONDITION_EQUALS;
                }
                break;
            case Condition::OPERATOR_NOT_EQUALS:
                if ($condition->value2->getType() == Value::TYPE_NULL) {
                    $operator = self::CONDITION_NOT_EQUALS_NULL;
                } else {
                    $operator = self::CONDITION_NOT_EQUALS;
                }
                break;
            case Condition::OPERATOR_GREATER_THAN:
                $operator = self::CONDITION_GREATER_THAN;
                break;
            case Condition::OPERATOR_GREATER_THAN_OR_EQUAL:
                $operator = self::CONDITION_GREATER_THAN_OR_EQUAL;
                break;
            case Condition::OPERATOR_LESS_THAN:
                $operator = self::CONDITION_LESS_THAN;
                break;
            case Condition::OPERATOR_LESS_THAN_OR_EQUAL:
                $operator = self::CONDITION_LESS_THAN_OR_EQUAL;
                break;
            case Condition::OPERATOR_IN:
                $operator = self::CONDITION_IN;
                break;
            case Condition::OPERATOR_NOT_IN:
                $operator = self::CONDITION_NOT_IN;
                break;
            default:
                throw new SQLException("Unsupported or illegal condition operator (" . $condition->operator . ").");
        }
        return $operator;
    }

    /**
     * Returns formatted table name for the ITable storage object.
     *
     * @param IDriver $driver database driver
     * @param ITable $storage storage object for which the result will be generated
     *
     * @return string formatted table name
     */
    public function getSelectFromTable(IDriver $driver, ITable $storage)
    {
        return $this->getSelectFromTableName($driver, $storage->getName());
    }

    /**
     * Formats table name from the raw table name string.
     *
     * @param IDriver $driver database driver
     * @param string $name table name
     *
     * @return string formatted table name
     */
    public function getSelectFromTableName(IDriver $driver, $name)
    {
        return $driver->tableQuoteStart . $name . $driver->tableQuoteEnd;
    }

    /**
     * Returns query object for full update statement.
     *
     * @param IDriver $driver database driver
     * @param IStore $storage database storage object
     * @param array $fields associative array of field => IValue to update
     * @param ICondition $condition optional database condition object, default NULL
     *
     * @return Query database query object
     */
    public function getUpdate(IDriver $driver, IStore $storage, array $fields, ICondition $condition = null)
    {
        $query = new Query(self::UPDATE);
        $query->addQuery($this->getSelectFrom($driver, $storage));
        $query->addString(self::UPDATE_SET);
        $query->addQuery($this->getUpdateFields($driver, $fields));
        if (!is_null($condition)) {
            $query->addString(self::WHERE);
            $query->addQuery($this->getCondition($driver, $condition));
        }
        return $query;
    }

    /**
     * Returns query object for "set" section of the update query.
     *
     * @param IDriver $driver database driver
     * @param array $fields associative array of field => IValue
     *
     * @return Query object
     */
    public function getUpdateFields(IDriver $driver, array $fields)
    {
        $query = new Query();
        $i = 0;
        foreach ($fields as $field => $value) {
            if ($i > 0) {
                $query->addString(self::UPDATE_FIELD_SEPARATOR, false);
            }
            $query->addString($this->getFieldFormatted($driver, $field));
            $query->addString(self::EQUALS);
            $query->addQuery($this->getValue($driver, $value));
            $i++;
        }
        return $query;
    }

    /**
     * Returns query object for full delete statement.
     *
     * @param IDriver $driver database driver
     * @param IStore $storage database storage object
     * @param ICondition $condition optional database condition object, default NULL
     *
     * @return Query database query object
     */
    public function getDelete(IDriver $driver, IStore $storage, ICondition $condition = null)
    {
        $query = new Query(self::DELETE . ' ' . self::FROM);
        $query->addQuery($this->getSelectFrom($driver, $storage));
        if (!is_null($condition)) {
            $query->addString(self::WHERE);
            $query->addQuery($this->getCondition($driver, $condition));
        }
        return $query;
    }

    /**
     * Returns query object for full insert statement.
     *
     * @param IDriver $driver database driver
     * @param IStore $storage database storage object
     * @param array $data associative array of field => IValue to insert
     *
     * @return Query database query object
     */
    public function getInsert(IDriver $driver, IStore $storage, array $data)
    {
        $query = new Query(self::INSERT . ' ' . self::INSERT_INTO);
        $query->addQuery($this->getSelectFrom($driver, $storage));
        $query->addString(self::INSERT_VALUES_WRAPPER_START);
        $query->addString($this->getInsertFields($driver, $data));
        $query->addString(self::INSERT_VALUES_WRAPPER_END);
        $query->addString(self::INSERT_VALUES);
        $query->addString(self::INSERT_VALUES_WRAPPER_START);
        $query->addQuery($this->getInsertValues($driver, $data));
        $query->addString(self::INSERT_VALUES_WRAPPER_END);
        return $query;
    }

    /**
     * Returns formatted string of fields for insert query.
     *
     * @param IDriver $driver database driver
     * @param array $data associative array of field => IValue to insert
     *
     * @return string formatted field list
     */
    public function getInsertFields(IDriver $driver, array $data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = $this->getFieldFormatted($driver, $key);
        }
        return implode(self::INSERT_FIELD_SEPARATOR, $fields);
    }

    /**
     * Gererates a query object for "values" section of the insert query.
     *
     * @param IDriver $driver database driver
     * @param array $data associative array of field => IValue to insert
     *
     * @return Query object
     */
    public function getInsertValues(IDriver $driver, array $data)
    {
        $query = new Query();
        $i = 0;
        foreach ($data as $value) {
            if ($i > 0) {
                $query->addString(self::INSERT_FIELD_SEPARATOR, false);
            }
            $query->addQuery($this->getValue($driver, $value));
            $i++;
        }
        return $query;
    }

}

?>
