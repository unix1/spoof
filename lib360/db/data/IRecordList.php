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
*	Database record interface.
*	This interface is used to define database data record.
*/

interface IRecordList
{

	/**
	*	Constructor.
	*	@param array $data optional array of IRecord database record objects
	*	@see IRecord
	*/
	public function __construct(array $data = array());

	/**
	*	Transforms object into XML representation.
	*	@return DOMDocument XML document object
	*/
	public function toXML();

}

?>