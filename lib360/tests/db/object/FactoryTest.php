<?php

namespace lib360\tests\db\object;

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
	*	@covers lib360\db\object\Factory::get
	*/
	public function testGet_InvalidType()
	{
		$e = NULL;
		try
		{
			\lib360\db\object\Factory::get('invalidtype', 'test');
		}
		catch (\lib360\db\object\TypeNotFoundException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\object\TypeNotFoundException', $e, "Failed to throw \lib360\db\object\TypeNotFoundException on invalid object type");
	}

	/**
	*	@covers lib360\db\object\Factory::get
	*/
	public function testGet_InvalidClass()
	{
		$e = NULL;
		try
		{
			\lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_LANGUAGE, 'invalid');
		}
		catch (\lib360\db\object\ClassNotFoundException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\object\ClassNotFoundException', $e, "Failed to throw \lib360\db\object\NotFoundException on invalid object name/class");
	}

	/**
	*	@covers lib360\db\object\Factory::get
	*/
	public function testGet_InvalidBaseClass()
	{
		$e = NULL;
		$driverTypeBackup = \lib360\db\object\Factory::getType('Driver');
		\lib360\db\object\Factory::setType('Driver', '\lib360\tests\db\object\\', '\lib360\db\driver\IDriver');
		\lib360\db\object\Factory::flushCache();
		try
		{
			\lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_DRIVER, 'NotExtendedDriver');
		}
		catch (\lib360\db\object\UnexpectedObjectTypeException $e)
		{
		}
		\lib360\db\object\Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
		$this->assertInstanceOf('\lib360\db\object\UnexpectedObjectTypeException', $e, "Failed to throw \lib360\db\object\NotFoundException on object not extending expected base");
	}

	/**
	*	@covers lib360\db\object\Factory::get
	*/
	public function testGet_SuccessNoException()
	{
		$e = NULL;
		try
		{
			$o = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_LANGUAGE, 'SQL');
		}
		catch (\lib360\db\Exception $e)
		{
		}
		$this->assertEquals(NULL, $e, "Caught an exception while retrieving an SQL language object");
	}

	/**
	*	@covers lib360\db\object\Factory::flushCache
	*	@depends testGet_SuccessNoException
	*/
	public function testFlushCache()
	{
		\lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_LANGUAGE, 'SQL');
		\lib360\db\object\Factory::flushCache();
		$actual = $this->getProtectedProperty('\lib360\db\object\Factory', 'objects')->getValue();
		$expected = array();
		$this->assertEquals($expected, $actual, "Failed to flush object cache");
		
	}

	/**
	*	@covers lib360\db\object\Factory::get
	*	@depends testFlushCache
	*/
	public function testGet_SuccessGeneral()
	{
		$driverTypeBackup = \lib360\db\object\Factory::getType('Driver');
		\lib360\db\object\Factory::setType('Driver', '\lib360\tests\db\object\\', '\lib360\db\driver\IDriver');
		\lib360\db\object\Factory::flushCache();
		$actual = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$expected = new CustomDriver();
		$this->assertEquals($expected, $actual, "Returned object from factory didn't match the newly instantiated object of same type");
		\lib360\db\object\Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

	/**
	*	@covers lib360\db\object\Factory::get
	*	@depends testFlushCache
	*/
	public function testGet_SuccessNew()
	{
		$driverTypeBackup = \lib360\db\object\Factory::getType('Driver');
		\lib360\db\object\Factory::setType('Driver', '\lib360\tests\db\object\\', '\lib360\db\driver\IDriver');
		\lib360\db\object\Factory::flushCache();
		$actual = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$expected = new CustomDriver();
		$this->assertEquals($expected, $actual, "Returned object from factory didn't match the newly instantiated object of same type");
		\lib360\db\object\Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

	/**
	*	@covers lib360\db\object\Factory::get
	*	@depends testGet_SuccessNew
	*/
	public function testGet_SuccessCache()
	{
		$driverTypeBackup = \lib360\db\object\Factory::getType('Driver');
		\lib360\db\object\Factory::setType('Driver', '\lib360\tests\db\connection\\', '\lib360\db\driver\IDriver');
		$expected = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$expected->testProperty = 'testvalue';
		$actual = \lib360\db\object\Factory::get(\lib360\db\object\Factory::OBJECT_TYPE_DRIVER, 'CustomDriver');
		$this->assertEquals($expected, $actual, "Returned object from factory did not come from cache");
		\lib360\db\object\Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

	/**
	*	@covers lib360\db\object\Factory::getType
	*/
	public function testGetType_Fail()
	{
		$e = NULL;
		try
		{
			\lib360\db\object\Factory::getType('nonexistent-type');
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Failed to throw an exception on invalid object type definition");
	}

	/**
	*	@covers lib360\db\object\Factory::getType
	*/
	public function testGetType_Success()
	{
		$type = 'Driver';
		$types = $this->getProtectedProperty('\lib360\db\object\Factory', 'types')->getValue();
		$expected = $types[$type];
		$actual = \lib360\db\object\Factory::getType($type);
		$this->assertEquals($expected, $actual, "Returned type definition didn't match internal values");
	}

	/**
	*	@covers lib360\db\object\Factory::setType
	*	@depends testGetType_Success
	*/
	public function testSetType()
	{
		$type = 'Driver';
		$expected = array('base' => '\lib360\tests\db\connection\\', 'interface' => '\lib360\db\driver\IDriver');
		$driverTypeBackup = \lib360\db\object\Factory::getType('Driver');
		\lib360\db\object\Factory::setType($type, $expected['base'], $expected['interface']);
		$actual = \lib360\db\object\Factory::getType($type);
		$this->assertEquals($expected, $actual, "Returned configuration didn't match the one set just prior");
		\lib360\db\object\Factory::setType($type, $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

}

?>