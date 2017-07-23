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

namespace spoof\tests\lib360\db\executor;

use spoof\lib360\db\connection\Pool;
use spoof\lib360\db\data\RecordList;
use spoof\lib360\db\executor\PreparedQueryException;
use spoof\lib360\db\object\Factory;
use spoof\lib360\db\value\Value;

class PDOTest extends \spoof\tests\lib360\db\DatabaseTestCase
{

    private function getDB()
    {
        return Pool::getByName(static::$db);
    }

    /**
     * @covers \spoof\lib360\db\executor\PDO::bindValues
     *    NOTE \PDOStatement cannot be properly unit tested because it lacks programatic access to internal data
     */
    public function testBindValues()
    {
        $dataSet = $this->getConnection()->createDataSet();
        $paramID = 1;
        $paramName = 'Numa';
        $queryString = "select * from user where id = :id or name_first = :name_first";
        $queryValues = array(
            'id' => new Value($paramID, Value::TYPE_INTEGER),
            'name_first' => new Value($paramName, Value::TYPE_STRING)
        );
        $sthActual = self::$pdo->prepare($queryString);
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        $ex->bindValues($sthActual, $queryValues);
        $sthActual->execute();
        $resultActual = $sthActual->fetchAll();
        $sthActual->closeCursor();
        $sthExpected = self::$pdo->prepare($queryString);
        $sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
        $sthExpected->bindValue(':name_first', $paramName, \PDO::PARAM_STR);
        $sthExpected->execute();
        $resultExpected = $sthExpected->fetchAll();
        $sthExpected->closeCursor();
        $this->assertEquals(
            array($sthExpected, $resultExpected),
            array($sthActual, $resultActual),
            "\PDOStatement or result didn't match expected objects"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::select
     * @covers  \spoof\lib360\db\executor\PDO::queryResults
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @depends testBindValues
     */
    public function testSelect_FailPrepare()
    {
        $e = null;
        $queryString = "Invalid SQL that will fail PDO prepare";
        $queryValues = array();
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->select($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid select SQL given"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::select
     * @covers  \spoof\lib360\db\executor\PDO::queryResults
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testSelect_FailExecute()
    {
        $e = null;
        $queryString = "select * from user where id = :id or name_first = :name_first";
        $queryValues = array('l' => 'this index does not exist and should fail during execution');
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->select($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid select SQL given for execution"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::select
     * @covers  \spoof\lib360\db\executor\PDO::queryResults
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testSelect_Success()
    {
        $dataSet = $this->getConnection()->createDataSet();
        $paramID = 1;
        $paramName = 'Numa';
        $queryString = "select * from user where id = :id or name_first = :name_first";
        $queryValues = array(
            'id' => new Value($paramID, Value::TYPE_INTEGER),
            'name_first' => new Value($paramName, Value::TYPE_STRING)
        );
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        $resultActual = $ex->select($this->getDB(), $queryString, $queryValues);
        $sthExpected = self::$pdo->prepare($queryString);
        $sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
        $sthExpected->bindValue(':name_first', $paramName, \PDO::PARAM_STR);
        $sthExpected->execute();
        $sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\spoof\lib360\db\data\Record', array(0 => null));
        $resultExpectedArray = $sthExpected->fetchAll();
        $sthExpected->closeCursor();
        $resultExpected = new RecordList($resultExpectedArray);
        $this->assertEquals($resultExpected, $resultActual, "Select result didn't match expected recordlist object");
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::update
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @depends testBindValues
     */
    public function testUpdate_FailPrepare()
    {
        $e = null;
        $queryString = "Invalid SQL that will fail PDO prepare";
        $queryValues = array();
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->update($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid update SQL given"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::update
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testUpdate_FailExecute()
    {
        $e = null;
        $paramID = 1;
        $paramName = 'Numa';
        $queryString = "update user set name_last = :name_last where id = :id or name_first = :name_first";
        $queryValues = array('l' => 'this index does not exist and should fail during execution');
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->update($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid update SQL given for execution"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::update
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testUpdate_Success()
    {
        $dataSet = $this->getConnection()->createDataSet();
        $paramID = 1;
        $paramName = 'Juno';
        $queryString = "update user set name_last = :name_last where id = :id";
        $queryValues = array(
            'id' => new Value($paramID, Value::TYPE_INTEGER),
            'name_last' => new Value($paramName, Value::TYPE_STRING)
        );
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        $resultActualNumRows = $ex->update($this->getDB(), $queryString, $queryValues);
        $resultActual = array($resultActualNumRows, $paramName);
        $sthExpected = self::$pdo->prepare("select name_last from user where id = :id");
        $sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
        $sthExpected->execute();
        $sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\spoof\lib360\db\data\Record', array(0 => null));
        $resultExpectedArray = $sthExpected->fetchAll();
        $sthExpected->closeCursor();
        $resultExpected = array(1, $resultExpectedArray[0]->name_last);
        $this->assertEquals($resultExpected, $resultActual, "Update call failed to update record to expected result");
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::insert
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @depends testBindValues
     */
    public function testInsert_FailPrepare()
    {
        $e = null;
        $queryString = "Invalid SQL that will fail PDO prepare";
        $queryValues = array();
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->insert($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid insert SQL given"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::insert
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testInsert_FailExecute()
    {
        $e = null;
        $paramID = 1;
        $paramName = 'Numa';
        $queryString = "insert into user (id, name_first, name_last) values (:id, :name_first, :name_last)";
        $queryValues = array('id' => 1, 'name_first' => 'test first', 'name_last' => 'test last');
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->insert($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid insert SQL given for execution"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::insert
     * @covers  \spoof\lib360\db\executor\PDO::queryLastInsertId
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testInsert_Success()
    {
        $dataSet = $this->getConnection()->createDataSet();
        $paramID = null;
        $paramNameFirst = 'Lola';
        $paramNameLast = 'Walker';
        $queryString = "insert into user (id, name_first, name_last) values(:id, :name_first, :name_last)";
        $queryValues = array(
            'id' => new Value($paramID, Value::TYPE_NULL),
            'name_first' => new Value($paramNameFirst, Value::TYPE_STRING),
            'name_last' => new Value($paramNameLast, Value::TYPE_STRING)
        );
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        $resultActualInsertedID = $ex->insert($this->getDB(), $queryString, $queryValues);
        $resultActualID = $this->getDB()->getConnection()->lastInsertId();
        $resultActual = array($resultActualInsertedID, $resultActualID, $paramNameFirst, $paramNameLast);
        $sthExpected = self::$pdo->prepare("select id, name_first, name_last from user where id = :id");
        $sthExpected->bindValue(':id', $resultActualID, \PDO::PARAM_INT);
        $sthExpected->execute();
        $sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\spoof\lib360\db\data\Record', array(0 => null));
        $resultExpectedArray = $sthExpected->fetchAll();
        $sthExpected->closeCursor();
        $resultExpected = array(
            $resultExpectedArray[0]->id,
            $resultExpectedArray[0]->id,
            $resultExpectedArray[0]->name_first,
            $resultExpectedArray[0]->name_last
        );
        $this->assertEquals($resultExpected, $resultActual, "Failed to verify inserted row");
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::delete
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @depends testBindValues
     */
    public function testDelete_FailPrepare()
    {
        $e = null;
        $queryString = "Invalid SQL that will fail PDO prepare";
        $queryValues = array();
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->delete($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid delete SQL given"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::delete
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testDelete_FailExecute()
    {
        $e = null;
        $queryString = "delete from user where id = :id";
        $queryValues = array('l' => 'this index does not exist and should fail during execution');
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->delete($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid delete SQL given for execution"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::delete
     * @covers  \spoof\lib360\db\executor\PDO::queryAffectedCount
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testDelete_Success()
    {
        $dataSet = $this->getConnection()->createDataSet();
        $paramID = 1;
        $queryString = "delete from user where id = :id";
        $queryValues = array('id' => new Value($paramID, Value::TYPE_INTEGER));
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        $resultActualNumRows = $ex->delete($this->getDB(), $queryString, $queryValues);
        $resultActual = array($resultActualNumRows, 0);
        $sthExpected = self::$pdo->prepare("select id from user where id = :id");
        $sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
        $sthExpected->execute();
        $sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\spoof\lib360\db\data\Record', array(0 => null));
        $resultExpectedArray = $sthExpected->fetchAll();
        $sthExpected->closeCursor();
        $resultExpected = array(1, count($resultExpectedArray));
        $this->assertEquals($resultExpected, $resultActual, "Delete call failed to delete expected record");
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::query
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @depends testBindValues
     */
    public function testQuery_FailPrepare()
    {
        $e = null;
        $queryString = "Invalid SQL that will fail PDO prepare";
        $queryValues = array();
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->query($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid SQL given"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::query
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testQuery_FailExecute()
    {
        $e = null;
        $queryString = "select * from user where id = :id or name_first = :name_first";
        $queryValues = array('l' => 'this index does not exist and should fail during execution');
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        try {
            $resultActual = $ex->query($this->getDB(), $queryString, $queryValues);
        } catch (PreparedQueryException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\executor\PreparedQueryException',
            $e,
            "Failed to throw \spoof\lib360\db\executor\PreparedQueryException when invalid SQL given for execution"
        );
    }

    /**
     * @covers  \spoof\lib360\db\executor\PDO::query
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementClose
     * @covers  \spoof\lib360\db\executor\PDO::queryStatementLive
     * @covers  \spoof\lib360\db\executor\PDO::getStatement
     * @covers  \spoof\lib360\db\executor\PDO::execute
     * @depends testBindValues
     */
    public function testQuery_Success()
    {
        $dataSet = $this->getConnection()->createDataSet();
        $paramID = 1;
        $queryString = "delete from user where id = :id";
        $queryValues = array('id' => new Value($paramID, Value::TYPE_INTEGER));
        $ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
        $ex->query($this->getDB(), $queryString, $queryValues);
        $sthExpected = self::$pdo->prepare("select id from user where id = :id");
        $sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
        $sthExpected->execute();
        $sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\spoof\lib360\db\data\Record', array(0 => null));
        $resultExpectedArray = $sthExpected->fetchAll();
        $sthExpected->closeCursor();
        $resultExpected = count($resultExpectedArray);
        $this->assertEquals($resultExpected, 0, "Delete query call failed to delete expected record");
    }

}

?>
