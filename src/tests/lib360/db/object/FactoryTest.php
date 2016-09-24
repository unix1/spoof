<?php

namespace spoof\tests\lib360\db\object;

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

use \spoof\lib360\db\object\ClassNotFoundException;
use \spoof\lib360\db\object\Factory;
use \spoof\lib360\db\object\TypeNotFoundException;
use \spoof\lib360\db\object\UnexpectedObjectTypeException;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::get
	*/
	public function testGet_InvalidType()
	{
		$e = NULL;
		try
		{
			Factory::get('invalidtype', 'test');
		}
		catch (TypeNotFoundException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\object\TypeNotFoundException', $e, "Failed to throw TypeNotFoundException on invalid object type");
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::get
	*/
	public function testGet_InvalidClass()
	{
		$e = NULL;
		try
		{
			Factory::get(Factory::OBJECT_TYPE_LANGUAGE, 'invalid');
		}
		catch (ClassNotFoundException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\object\ClassNotFoundException', $e, "Failed to throw ClassNotFoundException on invalid object name/class");
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::get
	*/
	public function testGet_InvalidBaseClass()
	{
		$e = NULL;
		$driverTypeBackup = Factory::getType('Driver');
		Factory::setType('Driver', '\spoof\tests\lib360\db\object\\', '\spoof\lib360\db\driver\IDriver');
		Factory::flushCache();
		try
		{
			Factory::get(Factory::OBJECT_TYPE_DRIVER, 'NotExtendedDriver');
		}
		catch (UnexpectedObjectTypeException $e)
		{
		}
		Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
		$this->assertInstanceOf('\spoof\lib360\db\object\UnexpectedObjectTypeException', $e, "Failed to throw UnexpectedObjectTypeException on object not extending expected base");
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::get
	*/
	public function testGet_SuccessNoException()
	{
		$e = NULL;
		try
		{
			$o = Factory::get(Factory::OBJECT_TYPE_LANGUAGE, 'SQL');
		}
		catch (\spoof\lib360\db\Exception $e)
		{
		}
		$this->assertEquals(NULL, $e, "Caught an exception while retrieving an SQL language object");
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::flushCache
	*	@depends testGet_SuccessNoException
	*/
	public function testFlushCache()
	{
		Factory::get(Factory::OBJECT_TYPE_LANGUAGE, 'SQL');
		Factory::flushCache();
		$actual = $this->getProtectedProperty('\spoof\lib360\db\object\Factory', 'objects')->getValue();
		$expected = array();
		$this->assertEquals($expected, $actual, "Failed to flush object cache");
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::get
	*	@depends testFlushCache
	*/
	public function testGet_SuccessGeneral()
	{
		$driverTypeBackup = Factory::getType('Driver');
		Factory::setType('Driver', '\spoof\tests\lib360\db\object\\', '\spoof\lib360\db\driver\IDriver');
		Factory::flushCache();
		$actual = Factory::get(Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$expected = new CustomDriver();
		$this->assertEquals($expected, $actual, "Returned object from factory didn't match the newly instantiated object of same type");
		Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::get
	*	@depends testFlushCache
	*/
	public function testGet_SuccessNew()
	{
		$driverTypeBackup = Factory::getType('Driver');
		Factory::setType('Driver', '\spoof\tests\lib360\db\object\\', '\spoof\lib360\db\driver\IDriver');
		Factory::flushCache();
		$actual = Factory::get(Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$expected = new CustomDriver();
		$this->assertEquals($expected, $actual, "Returned object from factory didn't match the newly instantiated object of same type");
		Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::get
	*	@depends testGet_SuccessNew
	*/
	public function testGet_SuccessCache()
	{
		$driverTypeBackup = Factory::getType('Driver');
		Factory::setType('Driver', '\spoof\tests\lib360\db\connection\\', '\spoof\lib360\db\driver\IDriver');
		$expected = Factory::get(Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$expected->testProperty = 'testvalue';
		$actual = Factory::get(Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$this->assertEquals($expected, $actual, "Returned object from factory did not come from cache");
		Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::getType
	*/
	public function testGetType_Fail()
	{
		$e = NULL;
		try
		{
			Factory::getType('nonexistent-type');
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Failed to throw an exception on invalid object type definition");
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::getType
	*/
	public function testGetType_Success()
	{
		$type = 'Driver';
		$types = $this->getProtectedProperty('\spoof\lib360\db\object\Factory', 'types')->getValue();
		$expected = $types[$type];
		$actual = Factory::getType($type);
		$this->assertEquals($expected, $actual, "Returned type definition didn't match internal values");
	}

	/**
	*	@covers \spoof\lib360\db\object\Factory::setType
	*	@depends testGetType_Success
	*/
	public function testSetType()
	{
		$type = 'Driver';
		$expected = array('base' => '\spoof\tests\lib360\db\connection\\', 'interface' => '\spoof\lib360\db\driver\IDriver');
		$driverTypeBackup = Factory::getType('Driver');
		Factory::setType($type, $expected['base'], $expected['interface']);
		$actual = Factory::getType($type);
		$this->assertEquals($expected, $actual, "Returned configuration didn't match the one set just prior");
		Factory::setType($type, $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

}

?>
