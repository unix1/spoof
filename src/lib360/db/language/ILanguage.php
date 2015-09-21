<?php

namespace spoof\lib360\db\language;

use \spoof\lib360\db\condition\ICondition;
use \spoof\lib360\db\data\IDataStore;
use \spoof\lib360\db\data\IStore;
use \spoof\lib360\db\driver\IDriver;

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
*	Database language interface
*/
interface ILanguage
{
	/**
	*	Gets query of the condition using driver-specific syntax.
	*
	*	@param IDriver $driver database driver
	*	@param ICondition $condition database condition object
	*
	*	@return \spoof\lib360\db\query\Query database query object
	*/
	public function getCondition(IDriver $driver, ICondition $condition);

	/**
	*	Returns query object for full select statement.
	*
	*	@param IDriver $driver database driver
	*	@param IDataStore $storage database storage object
	*	@param ICondition $condition optional database condition object, default NULL
	*	@param array $fields optional (optionally associative) array of fields to query and return, default NULL
	*
	*	@return \lib360\db\query\Query database query object
	*/
	public function getSelect(IDriver $driver, IStore $storage, ICondition $condition = NULL, array $fields = NULL);

	/**
	*	Returns query object for full update statement.
	*
	*	@param IDriver $driver database driver
	*	@param IStore $storage database storage object
	*	@param array $fields associative array of field => \spoof\lib360\db\value\IValue to update
	*	@param ICondition $condition optional database condition object, default NULL
	*
	*	@return \spoof\lib360\db\query\Query database query object
	*/
	public function getUpdate(IDriver $driver, IStore $storage, array $fields, ICondition $condition = NULL);

	/**
	*	Returns query object for full delete statement.
	*
	*	@param IDriver $driver database driver
	*	@param IStore $storage database storage object
	*	@param ICondition $condition optional database condition object, default NULL
	*
	*	@return \spoof\lib360\db\query\Query database query object
	*/
	public function getDelete(IDriver $driver, IStore $storage, ICondition $condition = NULL);

	/**
	*	Returns query object for full insert statement.
	*
	*	@param IDriver $driver database driver
	*	@param IStore $storage database storage object
	*	@param array $data associative array of field => \spoof\lib360\db\value\IValue to insert
	*	@return \spoof\lib360\db\query\Query database query object
	*/
	public function getInsert(IDriver $driver, IStore $storage, array $data);

}

?>
