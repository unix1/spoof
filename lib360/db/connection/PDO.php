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

class PDO extends Connection
{

	/**
	*	Connects the object to the database
	*	@return PDO connection
	*/
	public function connect()
	{
		$this->connection = new \PDO($this->config->dsn, $this->config->username, $this->config->password, $this->config->options);
		return $this->connection;
	}

}

?>