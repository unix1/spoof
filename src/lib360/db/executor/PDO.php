<?php

/**
 *  This is Spoof.
 *  Copyright (C) 2011-2017  Spoof project.
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

namespace spoof\lib360\db\executor;

use spoof\lib360\db\connection\IConnection;
use spoof\lib360\db\data\RecordList;
use spoof\lib360\db\value\IValue;
use spoof\lib360\db\value\Value;

/**
 *    PDO executor implementation
 */
class PDO implements IExecutor
{
    /**
     * Maps \spoof\lib360\db\value\Value types to PDO parameter types
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
     * Executes database select.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     * @param string $name optional name to use for identifying records
     *
     * @return \spoof\lib360\db\data\RecordList object
     *
     * @throws PreparedQueryException when database error occurs during query execution
     */
    public function select(IConnection $conn, $query, array $values = null, $name = null)
    {
        $records = $this->queryResults($conn, $query, $values, $name);
        $recordlist = new RecordList($records);
        return $recordlist;
    }

    /**
     * Executes query and gets resulting rows.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     * @param string $name optional name to use for identifying records
     *
     * @return array result database rows
     */
    private function queryResults(IConnection $conn, $query, $values = null, $name = null)
    {
        $sth = $this->queryStatementLive($conn, $query, $values);
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\spoof\lib360\db\data\Record', array(0 => $name));
        $records = $sth->fetchAll();
        $sth->closeCursor();
        return $records;
    }

    /**
     * Executes query and gets open statement handle.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @return \PDOStatement PDO statement handle object
     */
    private function queryStatementLive(IConnection $conn, $query, array $values = null)
    {
        $sth = $this->getStatement($conn, $query);
        $this->execute($sth, $values);
        return $sth;
    }

    /**
     * Gets a prepared query statement.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query string
     *
     * @return \PDOStatement PDO statement handle object
     *
     * @throws PreparedQueryException when database error occurs during statement creation
     */
    private function getStatement(IConnection $conn, $query)
    {
        $sth = $conn->getConnection()->prepare($query);
        if ($sth === false) {
            $error = $conn->getConnection()->errorInfo();
            throw new PreparedQueryException(
                "SQLState: " . $error[0] . ". Driver error code: " . $error[1] . ". Driver error message: " . $error[2] . "."
            );
        }
        return $sth;
    }

    /**
     * Executes query using prepared statement.
     *
     * @param \PDOStatement $sth
     * @param array $values
     *
     * @throws PreparedQueryException when database error occurs during query execution
     */
    private function execute(\PDOStatement $sth, array $values = null)
    {
        $this->bindValues($sth, $values);
        if (!$sth->execute()) {
            $error = $sth->errorInfo();
            throw new PreparedQueryException(
                "SQLState: " . $error[0] . ". Driver error code: " . $error[1] . ". Driver error message: " . $error[2] . "."
            );
        }
    }

    /**
     * Binds values to PDOStatement.
     *
     * @param \PDOStatement $sth PDO statement object to which values will be bound
     * @param array $values optional array of values (IValue or primitive types) for prepared statement
     */
    public function bindValues(\PDOStatement $sth, array $values = null)
    {
        if (!is_null($values)) {
            foreach ($values as $key => $value) {
                $type = \PDO::PARAM_STR;
                if ($value instanceof IValue) {
                    if (isset(self::$typeMap[$value->getType()])) {
                        $type = self::$typeMap[$value->getType()];
                    }
                    $value = $value->getValue();
                }
                $sth->bindValue(':' . $key, $value, $type);
            }
        }
    }

    /**
     * Executes database update.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @return integer number of rows updated
     *
     * @throws PreparedQueryException when database error occurs during query execution
     */
    public function update(IConnection $conn, $query, array $values = null)
    {
        return $this->queryAffectedCount($conn, $query, $values);
    }

    /**
     * Executes query and gets affected row count.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @return integer number of rows affected
     */
    private function queryAffectedCount(IConnection $conn, $query, array $values = null)
    {
        $sth = $this->queryStatementClose($conn, $query, $values);
        return $sth->rowCount();
    }

    /**
     * Executes query and returns last inserted ID.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @return mixed inserted row ID
     */
    private function queryLastInsertId(IConnection $conn, $query, array $values = null)
    {
        $sth = $this->queryStatementClose($conn, $query, $values);
        return $conn->getConnection()->lastInsertId();
    }

    /**
     *    Executes query and gets closed statement handle.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @return \PDOStatement PDO statement handle object
     */
    private function queryStatementClose(IConnection $conn, $query, array $values = null)
    {
        $sth = $this->queryStatementLive($conn, $query, $values);
        $sth->closeCursor();
        return $sth;
    }

    /**
     * Executes database insert.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @return mixed inserted row ID
     *
     * @throws PreparedQueryException when database error occurs during query execution
     */
    public function insert(IConnection $conn, $query, array $values = null)
    {
        return $this->queryLastInsertId($conn, $query, $values);
    }

    /**
     * Executes database delete.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @return integer number of rows deleted
     *
     * @throws PreparedQueryException when database error occurs during query execution
     */
    public function delete(IConnection $conn, $query, array $values = null)
    {
        return $this->queryAffectedCount($conn, $query, $values);
    }

    /**
     * Executes a generic database query.
     *
     * @param IConnection $conn database connection object
     * @param string $query prepared query statement
     * @param array $values optional array of values for prepared statement
     *
     * @throws PreparedQueryException when database error occurs during query execution
     */
    public function query(IConnection $conn, $query, array $values = null)
    {
        $this->queryStatementClose($conn, $query, $values);
    }

}

?>
