<?php

namespace spoof\tests\lib360\db\data;

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

use \spoof\lib360\db\condition\Condition;
use \spoof\lib360\db\condition\ConditionGroup;
use \spoof\lib360\db\connection\Config;
use \spoof\lib360\db\connection\PDO;
use \spoof\lib360\db\connection\Pool;
use \spoof\lib360\db\data\RecordList;
use \spoof\lib360\db\object\Factory;
use \spoof\lib360\db\value\Value;

class TableTest extends \spoof\tests\lib360\db\DatabaseTestCase
{

	protected static $tablesCreated = FALSE;
	protected static $db = 'test';

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
		Pool::add(new PDO(new Config($GLOBALS['DB_DSN'])), self::$db);
	}

	public function tearDown()
	{
		parent::tearDown();
		Pool::removeByName('test');
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::select
	*/
	public function testSelect_NoCondition()
	{
		$user = new HelperTableUser();
		$resultActual = $user->select();
		$ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultExpected = $ex->select(Pool::getByName(self::$db), "select * from user");
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select result (no condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::select
	*/
	public function testSelect_Condition()
	{
		$valueID = 1;
		$user = new HelperTableUser();
		$resultActual = $user->select(
				new Condition(
					new Value('id', Value::TYPE_COLUMN),
					Condition::OPERATOR_EQUALS,
					new Value($valueID, Value::TYPE_INTEGER)
				)
		);
		$ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultExpected = $ex->select(Pool::getByName(self::$db), "select * from user where id = " . $valueID);
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select result (with condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::select
	*/
	public function testSelect_ConditionValues()
	{
		$valueID = 1;
		$user = new HelperTableUser();
		$resultActual = $user->select(
				new Condition(
					new Value('id', Value::TYPE_COLUMN),
					Condition::OPERATOR_EQUALS,
					new Value('id', Value::TYPE_PREPARED)
				),
				array('id' => $valueID)
		);
		$ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultExpected = $ex->select(Pool::getByName(self::$db), "select * from user where id = " . $valueID);
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select result (with condition and values)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::select
	*/
	public function testSelect_ConditionFields()
	{
		$fields = array('name_first', 'name_last', 'id');
		$user = new HelperTableUser();
		$resultActual = $user->select(
				new Condition(
					new Value('id', Value::TYPE_COLUMN),
					Condition::OPERATOR_EQUALS,
					new Value(1, Value::TYPE_INTEGER)
				),
				array(),
				$fields
		);
		$ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultExpected = $ex->select(Pool::getByName(self::$db), "select name_first, name_last, id from user where id = 1", array(), $fields);
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select result (with condition and fields)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::selectRecords
	*	@depends testSelect_NoCondition
	*/
	public function testSelectRecords_NoCondition()
	{
		$user = new HelperTableUser();
		$resultActual = $user->selectRecords();
		$resultExpected = $user->select();
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select records result (no condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::selectRecords
	*	@depends testSelect_Condition
	*/
	public function testSelectRecords_ConditionOne()
	{
		$paramNameFirst = 'Numa';
		$user = new HelperTableUser();
		$resultActual = $user->selectRecords(array('name_first' => $paramNameFirst));
		$resultExpected = $user->select(
			new Condition(
				new Value('name_first', Value::TYPE_COLUMN),
				Condition::OPERATOR_EQUALS,
				new Value($paramNameFirst, Value::TYPE_STRING)
			)
		);
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select records result (with one condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::selectRecords
	*	@depends testSelect_Condition
	*/
	public function testSelectRecords_ConditionMany()
	{
		$paramNameFirst = 'Juno';
		$paramNameLast = 'Jiana';
		$user = new HelperTableUser();
		$resultActual = $user->selectRecords(array('name_first' => $paramNameFirst, 'name_last' => $paramNameLast));
		$c1 = new Condition(
			new Value('name_first', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($paramNameFirst, Value::TYPE_STRING)
		);
		$c2 = new Condition(
			new Value('name_last', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($paramNameLast, Value::TYPE_STRING)
		);
		$cg = new ConditionGroup($c1);
		$cg->addCondition(ConditionGroup::OPERATOR_AND, $c2);
		$resultExpected = $user->select($cg);
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select records result (with many conditions)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::selectRecords
	*/
	public function testSelectRecords_ConditionFields()
	{
		$paramID = 1;
		$fields = array('name_first', 'name_last', 'id');
		$user = new HelperTableUser();
		$resultActual = $user->selectRecords(array('id' => $paramID), $fields);
		$resultExpected = $user->select(
			new Condition(
				new Value('id', Value::TYPE_COLUMN),
				Condition::OPERATOR_EQUALS,
				new Value($paramID, Value::TYPE_INTEGER)
			),
			array(),
			$fields
		);
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected select records result (with condition and fields)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::update
	*	@depends testSelect_NoCondition
	*/
	public function testUpdate_NoCondition()
	{
		$valueNameFirst = 'test first';
		$valueNameLast = 'test last';
		$resultExpected = array();
		$resultActual = array();
		$user = new HelperTableUser();
		$resultActual['rows_updated'] = $user->update(array('name_first' => new Value($valueNameFirst, Value::TYPE_STRING), 'name_last' => new Value($valueNameLast, Value::TYPE_STRING)));
		// we do this here instead of table->select() because the easiest way to assert is to do select distinct
		// when table->select() has supoprt for select distinct it can be used instead
		$ex = Factory::get(Factory::OBJECT_TYPE_EXECUTOR, 'PDO');
		$resultActualRecords = $ex->select(Pool::getByName(self::$db), "select distinct name_first, name_last from user");
		$resultActual['records_count'] = count($resultActualRecords);
		$resultActual['name_first'] = $resultActualRecords[0]->name_first;
		$resultActual['name_last'] = $resultActualRecords[0]->name_last;
		$resultExpected['rows_updated'] = 4;
		$resultExpected['records_count'] = 1;
		$resultExpected['name_first'] = $valueNameFirst;
		$resultExpected['name_last'] = $valueNameLast;
		$this->assertEquals($resultExpected, $resultActual, "Failed to match update result (no condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::update
	*	@depends testSelect_ConditionFields
	*/
	public function testUpdate_Condition()
	{
		$valueNameFirst = 'test first';
		$valueNameLast = 'test last';
		$valueID = 1;
		$resultExpected = array();
		$resultActual = array();
		$user = new HelperTableUser();
		$cond = new Condition(
			new Value('id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($valueID, Value::TYPE_INTEGER)
		);
		$resultActual['rows_updated'] = $user->update(
			array('name_first' => new Value($valueNameFirst, Value::TYPE_STRING), 'name_last' => new Value($valueNameLast, Value::TYPE_STRING)),
			$cond
		);
		$rows = $user->select($cond, array(), array('name_first', 'name_last', 'id'));
		$resultActual['records_count'] = count($rows);
		$resultActual['name_first'] = $rows[0]->name_first;
		$resultActual['name_last'] = $rows[0]->name_last;
		$resultActual['id'] = $rows[0]->id;
		$resultExpected['rows_updated'] = 1;
		$resultExpected['records_count'] = 1;
		$resultExpected['name_first'] = $valueNameFirst;
		$resultExpected['name_last'] = $valueNameLast;
		$resultExpected['id'] = $valueID;
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected update result (with condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::select
	*	@depends testSelect_ConditionFields
	*/
	public function testUpdate_ConditionValues()
	{
		$valueNameFirst = 'test first';
		$valueNameLast = 'test last';
		$valueID = 1;
		$resultExpected = array();
		$resultActual = array();
		$user = new HelperTableUser();
		$cond = new Condition(
					new Value('id', Value::TYPE_COLUMN),
					Condition::OPERATOR_EQUALS,
					new Value('id', Value::TYPE_PREPARED)
		);
		$values = array('id' => new Value($valueID, Value::TYPE_INTEGER));
		$resultActual['rows_updated'] = $user->update(
				array('name_first' => new Value($valueNameFirst, Value::TYPE_STRING), 'name_last' => new Value($valueNameLast, Value::TYPE_STRING)),
				$cond,
				$values
		);
		$rows = $user->select($cond, $values, array('name_first', 'name_last', 'id'));
		$resultActual['records_count'] = count($rows);
		$resultActual['name_first'] = $rows[0]->name_first;
		$resultActual['name_last'] = $rows[0]->name_last;
		$resultActual['id'] = $rows[0]->id;
		$resultExpected['rows_updated'] = 1;
		$resultExpected['records_count'] = 1;
		$resultExpected['name_first'] = $valueNameFirst;
		$resultExpected['name_last'] = $valueNameLast;
		$resultExpected['id'] = $valueID;
		$this->assertEquals($resultExpected, $resultActual, "Failed to match expected update result (with condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::insert
	*	@depends testSelect_ConditionFields
	*/
	public function testInsert()
	{
		$valueNameFirst = 'test first';
		$valueNameLast = 'test last';
		$valueID = 5;
		$values = array(
			'id' => new Value($valueID, Value::TYPE_INTEGER),
			'name_first' => new Value($valueNameFirst, Value::TYPE_STRING),
			'name_last' => new Value($valueNameLast, Value::TYPE_STRING)
		);
		$resultExpected = array('rows' => 1, 'id' => $valueID, 'name_first' => $valueNameFirst, 'name_last' => $valueNameLast);
		$user = new HelperTableUser();
		$user->insert($values);
		$resultActualRows = $user->select(
			new Condition(
				new Value('id', Value::TYPE_COLUMN),
				Condition::OPERATOR_EQUALS,
				new Value($valueID, Value::TYPE_INTEGER)
			),
			array(),
			array('id', 'name_first', 'name_last')
		);
		$resultActual = array();
		$resultActual['rows'] = count($resultActualRows);
		$resultActual['id'] = $resultActualRows[0]->id;
		$resultActual['name_first'] = $resultActualRows[0]->name_first;
		$resultActual['name_last'] = $resultActualRows[0]->name_last;
		$this->assertEquals($resultExpected, $resultActual, "Select result didn't match the inserted values");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::delete
	*	@depends testSelect_NoCondition
	*/
	public function testDelete_NoCondition()
	{
		$resultExpected = new RecordList();
		$user = new HelperTableUser();
		$user->delete();
		$resultActual = $user->select();
		$this->assertEquals($resultExpected, $resultActual, "Select wasn't empty after delete (no condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::delete
	*	@depends testSelect_Condition
	*/
	public function testDelete_Condition()
	{
		$valueID = 4;
		$resultExpected = new RecordList();
		$user = new HelperTableUser();
		$cond = new Condition(
				new Value('id', Value::TYPE_COLUMN),
				Condition::OPERATOR_EQUALS,
				new Value($valueID, Value::TYPE_INTEGER)
		);
		$user->delete($cond);
		$resultActual = $user->select($cond);
		$this->assertEquals($resultExpected, $resultActual, "Select wasn't empty after delete (with condition)");
	}

	/**
	*	@covers \spoof\lib360\db\data\Table::delete
	*	@depends testSelect_ConditionValues
	*/
	public function testDelete_ConditionValues()
	{
		$valueID = 4;
		$values = array('id' => new Value($valueID, Value::TYPE_INTEGER));
		$resultExpected = new RecordList();
		$user = new HelperTableUser();
		$cond = new Condition(
				new Value('id', Value::TYPE_COLUMN),
				Condition::OPERATOR_EQUALS,
				new Value('id', Value::TYPE_PREPARED)
		);
		$user->delete($cond, $values);
		$resultActual = $user->select($cond, $values);
		$this->assertEquals($resultExpected, $resultActual, "Select wasn't empty after delete (with condition and values)");
	}

}

?>
