<?php

namespace lib360\tests\db\executor;

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

class PDOTest extends \lib360\tests\db\DatabaseTestCase
{

	protected static $tablesCreated = FALSE;
	protected $db;

	public function getDataSet()
	{
		if (!self::$tablesCreated)
		{
			self::$pdo->query('drop table if exists "user"');
			self::$pdo->query('create table user (id integer primary key autoincrement, date_created datetime null default null, name_first varchar(50), name_last varchar(50), status varchar(10) not null default \'\')');
			self::$tablesCreated = TRUE;
		}
		return $this->createXMLDataSet(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'test-data1.xml');
	}

	public function setUp()
	{
		parent::setUp();
		$this->db = new \lib360\db\connection\PDO(new \lib360\db\connection\Config($GLOBALS['DB_DSN']));
		$this->db->connect();
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->db->disconnect();
	}

	/**
	*	@covers \lib360\db\executor\PDO::bindValues
	*	NOTE \PDOStatement cannot be properly unit tested because it lacks programatic access to internal data
	*/
	public function testBindValues()
	{
		$dataSet = $this->getConnection()->createDataSet();
		$paramID = 1;
		$paramName = 'Numa';
		$queryString = "select * from user where id = :id or name_first = :name_first";
		$queryValues = array('id' => new \lib360\db\value\Value($paramID, \lib360\db\value\Value::TYPE_INTEGER), 'name_first' => new \lib360\db\value\Value($paramName, \lib360\db\value\Value::TYPE_STRING));
		$sthActual = self::$pdo->prepare($queryString);
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
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
		$this->assertEquals(array($sthExpected, $resultExpected), array($sthActual, $resultActual), "\PDOStatement or result didn't match expected objects");
	}

	/**
	*	@covers \lib360\db\executor\PDO::select
	*	@depends testBindValues
	*/
	public function testSelect_FailPrepare()
	{
		$e = NULL;
		$queryString = "Invalid SQL that will fail PDO prepare";
		$queryValues = array();
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->select($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid select SQL given");
	}

	/**
	*	@covers \lib360\db\executor\PDO::select
	*	@depends testBindValues
	*/
	public function testSelect_FailExecute()
	{
		$e = NULL;
		$queryString = "select * from user where id = :id or name_first = :name_first";
		$queryValues = array('l' => 'this index does not exist and should fail during execution');
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->select($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid select SQL given for execution");
	}

	/**
	*	@covers \lib360\db\executor\PDO::select
	*	@depends testBindValues
	*/
	public function testSelect_Success()
	{
		$dataSet = $this->getConnection()->createDataSet();
		$paramID = 1;
		$paramName = 'Numa';
		$queryString = "select * from user where id = :id or name_first = :name_first";
		$queryValues = array('id' => new \lib360\db\value\Value($paramID, \lib360\db\value\Value::TYPE_INTEGER), 'name_first' => new \lib360\db\value\Value($paramName, \lib360\db\value\Value::TYPE_STRING));
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultActual = $ex->select($this->db, $queryString, $queryValues);
		$sthExpected = self::$pdo->prepare($queryString);
		$sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
		$sthExpected->bindValue(':name_first', $paramName, \PDO::PARAM_STR);
		$sthExpected->execute();
		$sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\lib360\db\data\Record', array(0 => NULL));
		$resultExpectedArray = $sthExpected->fetchAll();
		$sthExpected->closeCursor();
		$resultExpected = new \lib360\db\data\RecordList($resultExpectedArray);
		$this->assertEquals($resultExpected, $resultActual, "Select result didn't match expected recordlist object");
	}

	/**
	*	@covers \lib360\db\executor\PDO::update
	*	@depends testBindValues
	*/
	public function testUpdate_FailPrepare()
	{
		$e = NULL;
		$queryString = "Invalid SQL that will fail PDO prepare";
		$queryValues = array();
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->update($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid update SQL given");
	}

	/**
	*	@covers \lib360\db\executor\PDO::update
	*	@depends testBindValues
	*/
	public function testUpdate_FailExecute()
	{
		$e = NULL;
		$paramID = 1;
		$paramName = 'Numa';
		$queryString = "update user set name_last = :name_last where id = :id or name_first = :name_first";
		$queryValues = array('l' => 'this index does not exist and should fail during execution');
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->update($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid update SQL given for execution");
	}

	/**
	*	@covers \lib360\db\executor\PDO::update
	*	@depends testBindValues
	*/
	public function testUpdate_Success()
	{
		$dataSet = $this->getConnection()->createDataSet();
		$paramID = 1;
		$paramName = 'Juno';
		$queryString = "update user set name_last = :name_last where id = :id";
		$queryValues = array('id' => new \lib360\db\value\Value($paramID, \lib360\db\value\Value::TYPE_INTEGER), 'name_last' => new \lib360\db\value\Value($paramName, \lib360\db\value\Value::TYPE_STRING));
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultActualNumRows = $ex->update($this->db, $queryString, $queryValues);
		$resultActual = array($resultActualNumRows, $paramName);
		$sthExpected = self::$pdo->prepare("select name_last from user where id = :id");
		$sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
		$sthExpected->execute();
		$sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\lib360\db\data\Record', array(0 => NULL));
		$resultExpectedArray = $sthExpected->fetchAll();
		$sthExpected->closeCursor();
		$resultExpected = array(1, $resultExpectedArray[0]->name_last);
		$this->assertEquals($resultExpected, $resultActual, "Update call failed to update record to expected result");
	}

	/**
	*	@covers \lib360\db\executor\PDO::insert
	*	@depends testBindValues
	*/
	public function testInsert_FailPrepare()
	{
		$e = NULL;
		$queryString = "Invalid SQL that will fail PDO prepare";
		$queryValues = array();
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->insert($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid insert SQL given");
	}

	/**
	*	@covers \lib360\db\executor\PDO::insert
	*	@depends testBindValues
	*/
	public function testInsert_FailExecute()
	{
		$e = NULL;
		$paramID = 1;
		$paramName = 'Numa';
		$queryString = "insert into user (id, name_first, name_last) values (:id, :name_first, :name_last)";
		$queryValues = array('id' => 1, 'name_first' => 'test first', 'name_last' => 'test last');
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->insert($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid insert SQL given for execution");
	}

	/**
	*	@covers \lib360\db\executor\PDO::insert
	*	@depends testBindValues
	*/
	public function testInsert_Success()
	{
		$dataSet = $this->getConnection()->createDataSet();
		$paramID = NULL;
		$paramNameFirst = 'Lola';
		$paramNameLast = 'Walker';
		$queryString = "insert into user (id, name_first, name_last) values(:id, :name_first, :name_last)";
		$queryValues = array('id' => new \lib360\db\value\Value($paramID, \lib360\db\value\Value::TYPE_NULL), 'name_first' => new \lib360\db\value\Value($paramNameFirst, \lib360\db\value\Value::TYPE_STRING), 'name_last' => new \lib360\db\value\Value($paramNameLast, \lib360\db\value\Value::TYPE_STRING));
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultActualNumRows = $ex->insert($this->db, $queryString, $queryValues);
		$resultActualID = $this->db->getConnection()->lastInsertId();
		$resultActual = array($resultActualNumRows, $resultActualID, $paramNameFirst, $paramNameLast);
		$sthExpected = self::$pdo->prepare("select id, name_first, name_last from user where id = :id");
		$sthExpected->bindValue(':id', $resultActualID, \PDO::PARAM_INT);
		$sthExpected->execute();
		$sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\lib360\db\data\Record', array(0 => NULL));
		$resultExpectedArray = $sthExpected->fetchAll();
		$sthExpected->closeCursor();
		$resultExpected = array(1, $resultExpectedArray[0]->id, $resultExpectedArray[0]->name_first, $resultExpectedArray[0]->name_last);
		$this->assertEquals($resultExpected, $resultActual, "Failed to verify inserted row");
	}

	/**
	*	@covers \lib360\db\executor\PDO::delete
	*	@depends testBindValues
	*/
	public function testDelete_FailPrepare()
	{
		$e = NULL;
		$queryString = "Invalid SQL that will fail PDO prepare";
		$queryValues = array();
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->delete($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid delete SQL given");
	}

	/**
	*	@covers \lib360\db\executor\PDO::delete
	*	@depends testBindValues
	*/
	public function testDelete_FailExecute()
	{
		$e = NULL;
		$queryString = "delete from user where id = :id";
		$queryValues = array('l' => 'this index does not exist and should fail during execution');
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->delete($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid delete SQL given for execution");
	}

	/**
	*	@covers \lib360\db\executor\PDO::delete
	*	@depends testBindValues
	*/
	public function testDelete_Success()
	{
		$dataSet = $this->getConnection()->createDataSet();
		$paramID = 1;
		$queryString = "delete from user where id = :id";
		$queryValues = array('id' => new \lib360\db\value\Value($paramID, \lib360\db\value\Value::TYPE_INTEGER));
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultActualNumRows = $ex->delete($this->db, $queryString, $queryValues);
		$resultActual = array($resultActualNumRows, 0);
		$sthExpected = self::$pdo->prepare("select id from user where id = :id");
		$sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
		$sthExpected->execute();
		$sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\lib360\db\data\Record', array(0 => NULL));
		$resultExpectedArray = $sthExpected->fetchAll();
		$sthExpected->closeCursor();
		$resultExpected = array(1, count($resultExpectedArray));
		$this->assertEquals($resultExpected, $resultActual, "Delete call failed to delete expected record");
	}

	/**
	*	@covers \lib360\db\executor\PDO::query
	*	@depends testBindValues
	*/
	public function testQuery_FailPrepare()
	{
		$e = NULL;
		$queryString = "Invalid SQL that will fail PDO prepare";
		$queryValues = array();
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->query($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid SQL given");
	}

	/**
	*	@covers \lib360\db\executor\PDO::query
	*	@depends testBindValues
	*/
	public function testQuery_FailExecute()
	{
		$e = NULL;
		$queryString = "select * from user where id = :id or name_first = :name_first";
		$queryValues = array('l' => 'this index does not exist and should fail during execution');
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		try
		{
			$resultActual = $ex->query($this->db, $queryString, $queryValues);
		}
		catch (\lib360\db\executor\PreparedQueryException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\executor\PreparedQueryException', $e, "Failed to throw \lib360\db\executor\PreparedQueryException when invalid SQL given for execution");
	}

	/**
	*	@covers \lib360\db\executor\PDO::query
	*	@depends testBindValues
	*/
	public function testQuery_Success()
	{
		$dataSet = $this->getConnection()->createDataSet();
		$paramID = 1;
		$queryString = "delete from user where id = :id";
		$queryValues = array('id' => new \lib360\db\value\Value($paramID, \lib360\db\value\Value::TYPE_INTEGER));
		$ex = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$ex->query($this->db, $queryString, $queryValues);
		$sthExpected = self::$pdo->prepare("select id from user where id = :id");
		$sthExpected->bindValue(':id', $paramID, \PDO::PARAM_INT);
		$sthExpected->execute();
		$sthExpected->setFetchMode(\PDO::FETCH_CLASS, '\lib360\db\data\Record', array(0 => NULL));
		$resultExpectedArray = $sthExpected->fetchAll();
		$sthExpected->closeCursor();
		$resultExpected = count($resultExpectedArray);
		$this->assertEquals($resultExpected, 0, "Delete query call failed to delete expected record");
	}

}

?>