<?php

namespace spoof\tests\lib360\db\data;

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

use \spoof\lib360\db\data\Record;

class RecordTest extends \PHPUnit_Framework_TestCase
{

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers \spoof\lib360\db\data\Record::__construct
	*/
	public function testConstruct_Default()
	{
		$o = new Record();
		$this->assertInstanceOf('\spoof\lib360\db\data\Record', $o, "Failed to create an instance");
	}

	/**
	*	@covers \spoof\lib360\db\data\Record::__construct
	*/
	public function testConstruct_CustomType()
	{
		$type = 'test';
		$o = new Record($type);
		$this->assertEquals($type, $this->getProtectedProperty('\spoof\lib360\db\data\Record', '__type')->getValue($o), "Failed to set custom type");
	}

	/**
	*	@covers \spoof\lib360\db\data\Record::__set
	*/
	public function testSet()
	{
		$testValue = 'test1';
		$o = new Record();
		$o->test1 = $testValue;
		$this->assertEquals($testValue, $o->offsetGet('test1'), "Failed to set value");
	}

	/**
	*	@covers \spoof\lib360\db\data\Record::__get
	*	@depends testSet
	*/
	public function testGet_Fail()
	{
		$o = new Record();
		$e = NULL;
		try
		{
			$v = $o->test;
		}
		catch (\OutOfBoundsException $e)
		{
		}
		$this->assertInstanceOf('\OutOfBoundsException', $e, "Failed to throw exception when offset doesn't exist");
	}

	/**
	*	@covers \spoof\lib360\db\data\Record::__get
	*	@depends testSet
	*/
	public function testGet_Success()
	{
		$o = new Record();
		$e = NULL;
		$v = NULL;
		$testValue = 'test value';
		$o->test = $testValue;
		try
		{
			$v = $o->test;
		}
		catch (\OutOfBoundsException $e)
		{
		}
		$this->assertEquals($testValue, $v, "Retrieved value doesn't match the value set");
	}

	/**
	*	@covers \spoof\lib360\db\data\Record::toXML
	*	@depends testSet
	*	@depends testGet_Success
	*/
	public function testToXML()
	{
		$o = new Record('test');
		$expectedXMLString = '<record type="test"><test1>test 1 value</test1><test2>test 2 value</test2></record>';
		$expectedXML = new \DOMDocument();
		$expectedXML->loadXML($expectedXMLString);
		$o->test1 = 'test 1 value';
		$o->test2 = 'test 2 value';
		$resultXML = $o->toXML();
		$this->assertEqualXMLStructure($expectedXML->firstChild, $resultXML->firstChild, TRUE, "Failed to return correct XML structure");
	}

}

?>