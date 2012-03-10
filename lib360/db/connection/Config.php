<?php

namespace lib360\db\connection;

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
*	A database configuration class.
*	This class is used to store database connection configuration options.
*/

class Config implements IConfig
{

	/**
	*	DSN string.
	*	DSN database connection string.
	*/
	public $dsn;

	/**
	*	Connection username.
	*	Username for the database connection.
	*/
	public $username;

	/**
	*	Connection password.
	*	Password for the database connection.
	*/
	public $password;

	/**
	*	Array of options.
	*	Array of database or connection specific options.
	*/
	public $options;

	/**
	*	Driver name string
	*/
	public $driver;

	/**
	*	Constructor for the DBConfig class instantiates the database connection configuration object
	*	@param string $dsn DSN for the connection
	*	@param string $username connection username, default NULL
	*	@param string $password connection password, default NULL
	*	@param array $options connections options, default NULL
	*	@param string $driver driver name, default NULL; set to override automatic detection from DSN string
	*/
	public function __construct($dsn, $username = NULL, $password = NULL, array $options = NULL, $driver = NULL)
	{
		$this->setDSN($dsn);
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setOptions($options);
		$this->setDriver($driver);
	}

	/**
	*	Sets DSN string for the connection
	*	@param string $dsn DSN for the connection
	*/
	public function setDSN($dsn)
	{
		$this->dsn = $dsn;
	}

	/**
	*	Sets username for the connections
	*	@param string $username username for the connection
	*/
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	*	Sets password for the connection
	*	@param string $password password for the connection
	*/
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	*	Sets array of options for the connection
	*	@param array $options connection specific options
	*/
	public function setOptions(array $options = NULL)
	{
		$this->options = $options;
	}

	/**
	*	Sets driver name for the configuration
	*	@param string $driver driver name
	*/
	public function setDriver($driver)
	{
		$this->driver = $driver;
	}

}

?>