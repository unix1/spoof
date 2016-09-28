<?php

namespace spoof\lib360\db\driver;

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
*	This is a template class for a database driver.
*	Implementing classes should define implementation specific values.
*/

class Driver implements IDriver
{

	/**
	*	Quote character prior to table name start
	*/
	public $tableQuoteStart;

	/**
	*	Quote character after table name end
	*/
	public $tableQuoteEnd;

	/**
	*	Quote character prior to column quote start
	*/
	public $columnQuoteStart;

	/**
	*	Quote character after column name end
	*/
	public $columnQuoteEnd;

	/**
	*	Character separator between table name and column name
	*/
	public $columnSeparator;

	/**
	*	String language to use with driver
	*/
	public $language;

	/**
	*	String executor to use with driver
	*/
	public $executor;

	/**
	*	Features array
	*
	*	Used to specify support for specific features.
	*	Extending classes should specify what features and levels they support.
	*/
	protected $features = array();

	/**
	*	Constructor
	*
	*	Implementing classes should initialize their values here.
	*/
	public function __construct()
	{
		$this->tableQuoteStart = '';
		$this->tableQuoteEnd = '';
		$this->columnQuoteStart = '';
		$this->columnQuoteEnd = '';
		$this->columnSeparator = '';
		$this->language = '';
		$this->executor = '';
	}

}

?>
