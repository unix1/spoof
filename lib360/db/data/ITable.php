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
*	Data table interface extends data storage interface.
*	Data table implementations must be based on this interface.
*/

interface ITable extends IStore
{

	/**
	*	Gets table records that match the supplied database condition.
	*	@param \lib360\db\condition\ICondition $condition optional condition to apply to the query
	*	@param array $values optional associative array of values for aliases in the condition object
	*	@param array $fields optional array of fields to return, can be associative for (table field) => (select as field) or a simple array of table field names, will override default $fields property
	*	@return IRecordList a database recordlist object, @see RecordList
	*	TODO add order/group by support
	*/
	public function select(\lib360\db\condition\ICondition $condition = NULL, array $values = NULL, array $fields = NULL);

	/**
	*	Gets table records by field criteria.
	*	@param array $conditions optional associative array of column names and their values to use as conditions, values will explicitly be cast as strings
	*	@param array $fields optional array of fields to return, can be associative for (table field) => (select as field) or a simple array of table field names, will override default $fields property
	*	@return IRecordList object containing matched rows @see RecordList
	*/
	public function selectRecords(array $conditions = array(), array $fields = NULL);

	/**
	*	Updates database record(s) based on supplied criteria and values.
	*	@param array $fields associative array of fields to update (table field) => (update value)
	*	@param \lib360\db\condition\ICondition $condition optional \lib360\db\condition\ICondition object to apply to the update
	*	@param array $values optional associative array of values for aliases in the condition object
	*	@return integer number of rows updated
	*/
	public function update(array $fields, \lib360\db\condition\ICondition $condition = NULL, array $values = array());

	/**
	*	Inserts a database record.
	*	@param array $fields associative array of fields for insert (table field) => (update value)
	*	@return integer number of rows inserted
	*/
	public function insert(array $fields);

	/**
	*	Deletes table records that match the supplied database condition.
	*	@param \lib360\db\condition\ICondition $condition optional condition to apply to the query
	*	@param array $values optional associative array of values for aliases in the condition object
	*	@return integer number of rows deleted
	*/
	public function delete(\lib360\db\condition\ICondition $condition = NULL, array $values = array());

}

?>