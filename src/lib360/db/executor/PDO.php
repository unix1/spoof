<?php

namespace spoof\lib360\db\executor;

use \spoof\lib360\db\connection\IConnection;
use \spoof\lib360\db\data\RecordList;
use \spoof\lib360\db\value\IValue;
use \spoof\lib360\db\value\Value;

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
*	PDO executor implementation
*/
class PDO implements IExecutor
{
	/**
	*	Maps \spoof\lib360\db\value\Value types to PDO parameter types
	*/
	public static $typeMap = array(
		Value::TYPE_BOOLEAN => \PDO::PARAM_BOOL,
		Value::TYPE_NULL => \PDO::PARAM_NULL,
		Value::TYPE_STRING => \PDO::PARAM_STR,
		Value::TYPE_INTEGER => \PDO::PARAM_INT,
		Value::TYPE_FLOAT => \PDO::PARAM_STR,
		Value::TYPE_BINARY => \PDO::PARAM_LOB
	);

	/**
	*	Executes database select.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*	@param string $name optional name to use for identifying records
	*
	*	@return \spoof\lib360\db\data\RecordList object
	*
	*	@throw PreparedQueryException when database error occurs during query execution
	*/
	public function select(IConnection $db, $query, array $values = NULL, $name = NULL)
	{
		$sth = $db->getConnection()->prepare($query);
		if ($sth === FALSE)
		{
			$error_array = $db->getConnection()->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$this->bindValues($sth, $values);
		if (!$sth->execute())
		{
			$error_array = $sth->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$sth->setFetchMode(\PDO::FETCH_CLASS, '\spoof\lib360\db\data\Record', array(0 => $name));
		$record_array = $sth->fetchAll();
		$recordlist = new RecordList($record_array);
		$sth->closeCursor();
		return $recordlist;
	}

	/**
	*	Executes database update.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@return integer number of rows updated
	*
	*	@throw PreparedQueryException when database error occurs during query execution
	*/
	public function update(IConnection $db, $query, array $values = NULL)
	{
		$sth = $db->getConnection()->prepare($query);
		if ($sth === FALSE)
		{
			$error_array = $db->getConnection()->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$this->bindValues($sth, $values);
		if (!$sth->execute())
		{
			$error_array = $sth->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$sth->closeCursor();
		return $sth->rowCount();
	}

	/**
	*	Executes database insert.
	*
	*	@param IConnection $db object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@return integer number of rows inserted
	*
	*	@throw PreparedQueryException when database error occurs during query execution
	*/
	public function insert(IConnection $db, $query, array $values = NULL)
	{
		$sth = $db->getConnection()->prepare($query);
		if ($sth === FALSE)
		{
			$error_array = $db->getConnection()->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$this->bindValues($sth, $values);
		if (!$sth->execute())
		{
			$error_array = $sth->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$sth->closeCursor();
		return $sth->rowCount();
	}

	/**
	*	Executes database delete.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@return integer number of rows deleted
	*
	*	@throw PreparedQueryException when database error occurs during query execution
	*/
	public function delete(IConnection $db, $query, array $values = NULL)
	{
		$sth = $db->getConnection()->prepare($query);
		if ($sth === FALSE)
		{
			$error_array = $db->getConnection()->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$this->bindValues($sth, $values);
		if (!$sth->execute())
		{
			$error_array = $sth->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$sth->closeCursor();
		return $sth->rowCount();
	}

	/**
	*	Executes a generic database query.
	*
	*	@param IConnection $db database connection object
	*	@param string $query prepared query statement
	*	@param array $values optional array of values for prepared statement
	*
	*	@throw PreparedQueryException when database error occurs during query execution
	*/
	public function query(IConnection $db, $query, array $values = NULL)
	{
		$sth = $db->getConnection()->prepare($query);
		if ($sth === FALSE)
		{
			$error_array = $db->getConnection()->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$this->bindValues($sth, $values);
		if (!$sth->execute())
		{
			$error_array = $sth->errorInfo();
			throw new PreparedQueryException("SQLState: " . $error_array[0] . ". Driver error code: " . $error_array[1] . ". Driver error message: " . $error_array[2] . ".");
		}
		$sth->closeCursor();
		//return $sth->rowCount();
	}

	/**
	*	Binds values to PDOStatement.
	*
	*	@param \PDOStatement $sth PDO statement object to which values will be bound
	*	@param array $values optional array of values (IValue or primitive types) for prepared statement
	*/
	public function bindValues(\PDOStatement $sth, array $values = NULL)
	{
		if (!is_null($values))
		{
			foreach ($values as $key => $value)
			{
				$type = \PDO::PARAM_STR;
				if ($value instanceof IValue)
				{
					if (isset(self::$typeMap[$value->getType()]))
					{
						$type = self::$typeMap[$value->getType()];
					}
					$value = $value->getValue();
				}
				$sth->bindValue(':' . $key, $value, $type);
			}
		}
	}

}

?>
