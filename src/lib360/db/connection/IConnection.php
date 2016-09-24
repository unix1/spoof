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
*	Database connection interface.
*	This interface is used to define database connection.
*/
interface IConnection
{

	/**
	*	Constructor for the database connection object instantiates the object
	*	but does not connect it to a database.
	*
	*	@param $config IDBConfig database connection configuration object
	*/
	public function __construct(IConfig $config);

	/**
	*	Connects the object to the database.
	*
	*	@return mixed connection object
	*/
	public function connect();

	/**
	*	Checks whether the database connection is active.
	*
	*	@return	boolean TRUE if connected, FALSE otherwise
	*/
	public function isConnected();

	/**
	*	Closes the connection.
	*/
	public function disconnect();

	/**
	*	Get feature support level.
	*
	*	@param string $feature
	*
	*	@return mixed feature support level
	*/
	public function getFeatureLevel($feature);

}

?>
