<?php

namespace lib360\db\language;

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
	*	@param \lib360\db\driver\IDriver $driver database driver
	*	@param \lib360\db\condition\ICondition $condition database condition object
	*	@return \lib360\db\query\Query database query object
	*/
	public function getCondition(\lib360\db\driver\IDriver $driver, \lib360\db\condition\ICondition $condition);

	/**
	*	Returns query object for full select statement
	*	@param \lib360\db\driver\IDriver $driver database driver
	*	@param \lib360\db\data\IDataStore $storage database storage object
	*	@param \lib360\db\condition\ICondition $condition optional database condition object, default NULL
	*	@param array $fields optional (optionally associative) array of fields to query and return, default NULL
	*	@return \lib360\db\query\Query database query object
	*/
	public function getSelect(\lib360\db\driver\IDriver $driver, \lib360\db\data\IStore $storage, \lib360\db\condition\ICondition $condition = NULL, array $fields = NULL);

	/**
	*	Returns query object for full update statement
	*	@param \lib360\db\driver\IDriver $driver database driver
	*	@param \lib360\db\data\IStore $storage database storage object
	*	@param array $fields associative array of field => \lib360\db\value\IValue to update
	*	@param \lib360\db\condition\ICondition $condition optional database condition object, default NULL
	*	@return \lib360\db\query\Query database query object
	*/
	public function getUpdate(\lib360\db\driver\IDriver $driver, \lib360\db\data\IStore $storage, array $fields, \lib360\db\condition\ICondition $condition = NULL);

	/**
	*	Returns query object for full delete statement
	*	@param \lib360\db\driver\IDriver $driver database driver
	*	@param \lib360\db\data\IStore $storage database storage object
	*	@param \lib360\db\condition\ICondition $condition optional database condition object, default NULL
	*	@return \lib360\db\query\Query database query object
	*/
	public function getDelete(\lib360\db\driver\IDriver $driver, \lib360\db\data\IStore $storage, \lib360\db\condition\ICondition $condition = NULL);

	/**
	*	Returns query object for full insert statement
	*	@param \lib360\db\driver\IDriver $driver database driver
	*	@param \lib360\db\data\IStore $storage database storage object
	*	@param array $data associative array of field => \lib360\db\value\IValue to insert
	*	@return \lib360\db\query\Query database query object
	*/
	public function getInsert(\lib360\db\driver\IDriver $driver, \lib360\db\data\IStore $storage, array $data);

}

?>