<?php

namespace spoof\lib360\db\executor;

use \spoof\lib360\db\connection\IConnection;

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
*	This defines interface for executor implementations.
*	Executors are used to execute queries against database systems.
*/
interface IExecutor
{
	/**
	*	Executes database select.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*	@param string $name optional name to use for identifying records
	*
	*	@return \lib360\db\data\IRecordList object
	*
	*	@throw \spoof\lib360\db\Exception when database error occurs during query execution
	*/
	public function select(IConnection $db, $query, array $values = NULL, $name = NULL);

	/**
	*	Executes database update.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@return integer number of rows updated
	*
	*	@throw \spoof\lib360\db\Exception when database error occurs during query execution
	*/
	public function update(IConnection $db, $query, array $values = NULL);

	/**
	*	Executes database insert.
	*
	*	@param IConnection $db object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@return integer number of rows inserted
	*
	*	@throw \spoof\lib360\db\Exception when database error occurs during query execution
	*/
	public function insert(IConnection $db, $query, array $values = NULL);

	/**
	*	Executes database delete.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@return integer number of rows deleted
	*
	*	@throw \lib360\db\Exception when database error occurs during query execution
	*/
	public function delete(IConnection $db, $query, array $values = NULL);

	/**
	*	Executes a generic database query.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@throw \lib360\db\Exception when database error occurs during query execution
	*/
	public function query(IConnection $db, $query, array $values = NULL);

}

?>
