<?php

namespace lib360\db\data;

/*
    This is Spoof.
    Copyright (C) 2011-2012  Spoof project.

    Spoof is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Spoof is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Spoof.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
*	A database table class.
*	Provides functionality for simple operations against a database table.
*	This class can either be extended, or instantiated dynamically via TableFactory.
*	@see TableFactory
*/

class Table extends Store implements ITable
{

	/**
	*	Table keys array.
	*	Extending classes will need to define a simple array listing table's primary keys as element values.
	*	NOTE currently not used
	*	NOTE this will be useful when implementing selectRecord/updateRecord methods
	*/
	protected $keys;

	/**
	*	Optional view default fields array.
	*	Extending classes may optionally (but recommended) define the list of fields that will be returned by default.
	*	This default can be overriden in the select method.
	*/
	public $fields = array();

	/**
	*	Gets table records that match the supplied database condition.
	*	@param \lib360\db\condition\ICondition $condition optional condition to apply to the query
	*	@param array $values optional associative array of values for aliases in the condition object
	*	@param array $fields optional array of fields to return, can be associative for (table field) => (select as field) or a simple array of table field names, will override default $fields property
	*	@return \lib360\db\data\IRecordList a database recordlist object, @see \lib360\db\data\RecordList
	*	TODO add order/group by support
	*/
	public function select(\lib360\db\condition\ICondition $condition = NULL, array $values = array(), array $fields = NULL)
	{
		// get connection object
		$db = \lib360\db\connection\Pool::getByName($this->db);
		// get language and query
		$return_fields = is_null($fields) ? $this->fields : $fields;
		$query = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getSelect($db->driver, $this, $condition, $return_fields);
		// get executor and execute
		$result = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->select($db, $query->query, $query->values + $values, $this->name);
		// return result
		return $result;
	}

	/**
	*	Gets table records by field criteria.
	*	@param array $conditions optional associative array of column names and their values to use as conditions, values will explicitly be cast as strings
	*	@param array $fields optional array of fields to return, can be associative for (table field) => (select as field) or a simple array of table field names, will override default $fields property
	*	@return IRecordList object containing matched rows @see RecordList
	*/
	public function selectRecords(array $conditions = array(), array $fields = NULL)
	{
		$condition_group = NULL;
		if (count($conditions) == 1)
		{
			foreach ($conditions as $column => $value)
			{
				$condition_group = new \lib360\db\condition\Condition(new \lib360\db\value\Value($column, \lib360\db\value\Value::TYPE_COLUMN), \lib360\db\condition\Condition::OPERATOR_EQUALS, new \lib360\db\value\Value((string) $value, \lib360\db\value\Value::TYPE_STRING));
			}
		}
		elseif (count($conditions) > 1)
		{
			foreach ($conditions as $column => $value)
			{
				$condition = new \lib360\db\condition\Condition(new \lib360\db\value\Value($column, \lib360\db\value\Value::TYPE_COLUMN), \lib360\db\condition\Condition::OPERATOR_EQUALS, new \lib360\db\value\Value((string) $value, \lib360\db\value\Value::TYPE_STRING));
				if (is_null($condition_group))
				{
					$condition_group = new \lib360\db\condition\ConditionGroup($condition);
				}
				else
				{
					$condition_group->addCondition(\lib360\db\condition\ConditionGroup::OPERATOR_AND, $condition);
				}
			}
		}
		return $this->select($condition_group, array(), $fields);
	}

	/**
	*	Updates database record(s) based on supplied criteria and values.
	*	@param array $fields associative array of fields to update (table field) => (update value)
	*	@param \lib360\db\condition\ICondition $condition optional \lib360\db\condition\ICondition object to apply to the update
	*	@param array $values optional associative array of values for aliases in the condition object
	*	@return integer number of rows updated
	*/
	public function update(array $fields, \lib360\db\condition\ICondition $condition = NULL, array $values = array())
	{
		// get connection object
		$db = \lib360\db\connection\Pool::getByName($this->db);
		// get language and query
		$query = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getUpdate($db->driver, $this, $fields, $condition);
		// get executor and execute
		$result = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->update($db, $query->query, $query->values + $values);
		// return result
		return $result;
	}

	/**
	*	Inserts a database record.
	*	@param array $fields associative array of fields for insert (table field) => (update value)
	*	@return integer number of rows inserted
	*/
	public function insert(array $fields)
	{
		// get connection object
		$db = \lib360\db\connection\Pool::getByName($this->db);
		// get language and query
		$query = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getInsert($db->driver, $this, $fields);
		// get executor and execute
		$result = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->insert($db, $query->query, $query->values);
		// return result
		return $result;
	}

	/**
	*	Deletes table records that match the supplied database condition.
	*	@param \lib360\db\condition\ICondition $condition optional condition to apply to the query
	*	@param array $values optional associative array of values for aliases in the condition object
	*	@return integer number of rows deleted
	*/
	public function delete(\lib360\db\condition\ICondition $condition = NULL, array $values = array())
	{
		// get connection object
		$db = \lib360\db\connection\Pool::getByName($this->db);
		// get language and query
		$query = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_LANGUAGE, $this->getLanguage())->getDelete($db->driver, $this, $condition);
		// get executor and execute
		$result = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, $this->getExecutor())->delete($db, $query->query, $query->values + $values);
		// return result
		return $result;
	}

}

?>