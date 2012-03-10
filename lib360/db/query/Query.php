<?php

namespace lib360\db\query;

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
*	Database query class
*	This class is used by language implementations to pass instructions
*	to executor implementations.
*/

class Query implements IQuery
{

	/**
	*	String query property
	*/
	public $query;

	/**
	*	Array values property
	*/
	public $values;

	/**
	*	Constructor
	*	@param string $query initial query string, optional, default NULL
	*	@param array $values initial values associative array, optional, default NULL
	*/
	public function __construct($query = NULL, array $values = NULL)
	{
		if (!is_null($query))
		{
			$this->query = $query;
		}
		else
		{
			$this->query = '';
		}
		if (!is_null($values))
		{
			$this->values = $values;
		}
		else
		{
			$this->values = array();
		}
	}

	/**
	*	Adds query object to current query object
	*	@param IQuery $query object to add
	*/
	public function addQuery(IQuery $query)
	{
		$this->addString($query->query);
		$this->addValues($query->values);
	}

	/**
	*	Adds string to query object
	*	@param string $query string to add
	*	@param $hintSpace hints use of space prior to appending, optional, default TRUE
	*/
	public function addString($query, $hintSpace = TRUE)
	{
		$this->query .= (($this->query === '' || !$hintSpace) ? '' : ' ') . $query;
	}

	/**
	*	Adds values array to query object
	*	@param array $values array to append
	*/
	public function addValues(array $values)
	{
		$this->values += $values;
	}

	/**
	*	Sets string query value
	*	@param string $query string to set
	*/
	public function setString($query)
	{
		$this->query = $query;
	}

}

?>