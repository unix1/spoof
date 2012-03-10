<?php

namespace lib360\tests\db\query;

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

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'initialize.php');

class QueryTest extends \PHPUnit_Framework_TestCase
{

	/**
	*	@covers \lib360\db\query\Query::__construct
	*/
	public function testConstruct_Default()
	{
		$q = new \lib360\db\query\Query();
		$actual = array($q->query, $q->values);
		$expected = array('', array());
		$this->assertEquals($expected, $actual, "Failed to initialize with default values");
	}

	/**
	*	@covers \lib360\db\query\Query::__construct
	*/
	public function testConstruct_Custom()
	{
		$queryString = 'test string';
		$queryValues = array('key1' => 'value1', 'key2' => 'value2');
		$q = new \lib360\db\query\Query($queryString, $queryValues);
		$actual = array($q->query, $q->values);
		$expected = array($queryString, $queryValues);
		$this->assertEquals($expected, $actual, "Failed to initialize with custom supplied values");
	}

	/**
	*	@covers \lib360\db\query\Query::setString
	*/
	public function testSetString_New()
	{
		$query = 'test string';
		$q = new \lib360\db\query\Query();
		$q->setString($query);
		$this->assertEquals($query, $q->query, "The string property of the object didn't match the string that was initially set");
	}

	/**
	*	@covers \lib360\db\query\Query::setString
	*	@depends testSetString_New
	*/
	public function testSetString_Override()
	{
		$query = 'test string override';
		$q = new \lib360\db\query\Query();
		$q->setString('test string 1');
		$q->setString($query);
		$this->assertEquals($query, $q->query, "The string property of the object didn't match the string that was set to override existing value");
	}

	/**
	*	@covers \lib360\db\query\Query::addString
	*	@depends testSetString_New
	*/
	public function testAddString_InitialHintTrue()
	{
		$query = 'test string';
		$q = new \lib360\db\query\Query();
		$q->addString($query, TRUE);
		$this->assertEquals($query, $q->query, "Failed to add initial string with space hint");
	}

	/**
	*	@covers \lib360\db\query\Query::addString
	*	@depends testSetString_New
	*/
	public function testAddString_InitialHintFalse()
	{
		$query = 'test string';
		$q = new \lib360\db\query\Query();
		$q->addString($query, FALSE);
		$this->assertEquals($query, $q->query, "Failed to add initial string with no space hint");
	}

	/**
	*	@covers \lib360\db\query\Query::addString
	*	@depends testSetString_New
	*/
	public function testAddString_AdditionalHintTrue()
	{
		$query1 = 'test string';
		$query2 = 'test additional string';
		$q = new \lib360\db\query\Query();
		$q->setString($query1);
		$q->addString($query2, TRUE);
		$this->assertEquals($query1 . ' ' . $query2, $q->query, "Failed to add additional string with space hint");
	}

	/**
	*	@covers \lib360\db\query\Query::addString
	*	@depends testSetString_New
	*/
	public function testAddString_AdditionalHintFalse()
	{
		$query1 = 'test string';
		$query2 = 'test additional string';
		$q = new \lib360\db\query\Query();
		$q->setString($query1);
		$q->addString($query2, FALSE);
		$this->assertEquals($query1 . $query2, $q->query, "Failed to add additional string with no space hint");
	}

	/**
	*	@covers \lib360\db\query\Query::addValues
	*/
	public function testAddValues_Initial()
	{
		$q = new \lib360\db\query\Query();
		$expected = array('key1' => 'value1', 'key2' => 'value2');
		$q->addValues($expected);
		$this->assertEquals($expected, $q->values, "Failed to add initial values");
	}

	/**
	*	@covers \lib360\db\query\Query::addValues
	*	@depends testAddValues_Initial
	*/
	public function testAddValues_Additional()
	{
		$q = new \lib360\db\query\Query();
		$arr1 = array('key1' => 'value1', 'key2' => 'value2');
		$arr2 = array('key3' => 'value3', 'key4' => 'value4');
		$q->addValues($arr1);
		$q->addValues($arr2);
		$expected = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3', 'key4' => 'value4');
		$this->assertEquals($expected, $q->values, "Failed to add additional values");
	}

	/**
	*	@covers \lib360\db\query\Query::addQuery
	*/
	public function testAddQuery()
	{
		$arrInitial = array('one' => 'value', 'two' => 'another value');
		$queryInitial = 'Initial query string';
		$q = new \lib360\db\query\Query($queryInitial, $arrInitial);
		$arrAdd = array('key1' => 'value1', 'key2' => 'value2');
		$queryAdd = 'Test query string';
		$qAdd = new \lib360\db\query\Query($queryAdd, $arrAdd);
		$q->addQuery($qAdd);
		$expected = array(
				array('one' => 'value', 'two' => 'another value', 'key1' => 'value1', 'key2' => 'value2'),
				$queryInitial . ' ' . $queryAdd
		);
		$this->assertEquals($expected, array($q->values, $q->query), "Failed to add query object");
	}

}

?>