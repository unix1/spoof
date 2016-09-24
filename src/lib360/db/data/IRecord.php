<?php

namespace spoof\lib360\db\data;

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
*	Database record interface.
*	This interface is used to define database data record.
*/
interface IRecord
{
	/**
	*	Constructor
	*
	*	@param string $type optional type, default 'Record'
	*/
	public function __construct($type = 'Record');

	/**
	*	Sets key-value association.
	*
	*	@param string $key
	*	@param mixed $value
	*/
	public function __set($key, $value);

	/**
	*	Gets associated value of the supplied key.
	*
	*	@param string $key
	*
	*	@return mixed associated value
	*
	*	@throw OutOfBoundsException when key doesn't exist
	*/
	public function __get($key);

	/**
	*	Transforms object into XML representation.
	*
	*	@return DOMDocument XML document object
	*/
	public function toXML();

}

?>
