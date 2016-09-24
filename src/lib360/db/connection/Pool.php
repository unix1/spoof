<?php

namespace spoof\lib360\db\connection;

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
*	Connection pooling implementation.
*
*	This static class provides connection pooling functionality for database
*	connections.
*/
class Pool implements IPool
{
	/**
	*	A connections array.
	*	Stores connection objects for internal use.
	*/
	protected static $connections = array();

	/**
	*	Adds the specified connection to the connection pool and assigns the
	*	specified alias.
	*
	*	@param IConnection $conn database connection object
	*	@param string $name name alias for the connection
	*
	*	@throw InvalidArgumentException when supplied alias already exists
	*/
	public static function add(IConnection $conn, $name)
	{
		if (isset(self::$connections[$name]))
		{
			throw new \InvalidArgumentException("Connection by the name $name already exists.");
		}
		self::$connections[$name] = $conn;
	}

	/**
	*	Retrieves previously stored database connection object by its alias.
	*
	*	@param string $name connection name
	*	@param boolean $connect if TRUE and not yet connected the database
	*	connection will be attempted, default TRUE
	*
	*	@return Connection object
	*
	*	@throw InvalidArgumentException when supplied alias is not found in the pool
	*/
	public static function getByName($name, $connect = TRUE)
	{
		if (!isset(self::$connections[$name]))
		{
			throw new \InvalidArgumentException("Connection by the name $name does not exist.");
		}
		if (!self::$connections[$name]->isConnected() && $connect)
		{
			self::$connections[$name]->connect();
		}
		return self::$connections[$name];
	}

	/**
	*	Removes connection from pool.
	*
	*	@param string $name connection name
	*	@param boolean $disconnect whether to disconnect (if connected) first, default TRUE
	*
	*	@throw \InvalidArgumentException when supplied alias is not found in the pool
	*/
	public static function removeByName($name, $disconnect = TRUE)
	{
		try
		{
			$connection = self::getByName($name, FALSE);
		}
		catch (\InvalidArgumentException $e)
		{
			throw new \InvalidArgumentException("Connection by the name $name does not exist");
		}
		if ($disconnect && $connection->isConnected())
		{
			$connection->disconnect();
		}
		unset(self::$connections[$name]);
	}

	/**
	*	Closes all active connections in the pool.
	*/
	public static function closeAll()
	{
		foreach (self::$connections as $connection)
		{
			if ($connection->isConnected())
			{
				$connection->disconnect();
			}
		}
	}

}

?>
