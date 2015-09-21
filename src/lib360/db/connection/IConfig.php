<?php

namespace spoof\lib360\db\connection;

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
*	Database connection configuration interface.
*	This interface is used to define database connection configuration.
*/
interface IConfig
{

	/**
	*	Constructor instantiates the database connection configuration object.
	*
	*	@param string $dsn DSN for the connection
	*	@param string $username connection username, optional, default NULL
	*	@param string $password connection password, optional, default NULL
	*	@param array $options connections options, optional, default NULL
	*/
	public function __construct($dsn, $username = NULL, $password = NULL,
								array $options = NULL);

	/**
	*	Sets DSN string for the connection.
	*
	*	@param string $dsn DSN for the connection
	*/
	public function setDSN($dsn);

	/**
	*	Sets username for the connections.
	*
	*	@param string $username username for the connection
	*/
	public function setUsername($username);

	/**
	*	Sets password for the connection.
	*
	*	@param string $password password for the connection
	*/
	public function setPassword($password);

	/**
	*	Sets array of options for the connection.
	*
	*	@param array $options connection specific options
	*/
	public function setOptions(array $options = NULL);

}

?>
