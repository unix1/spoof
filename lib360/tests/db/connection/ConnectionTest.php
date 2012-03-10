<?php

namespace lib360\tests\db\connection;

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

class ConnectionTest extends \PHPUnit_Framework_TestCase
{

	public $dbconfigBad1;
	public $dbconfigBad2;
	public $dbconfig1;
	public $dbconfig2;
	public $dbconfigCustomDriver;

	public function setUp()
	{
		$this->dbconfigBad1 = new \lib360\db\connection\Config('mysql');
		$this->dbconfigBad2 = new \lib360\db\connection\Config('fake:localhost');
		$this->dbconfigCustomDriver = new \lib360\db\connection\Config('mysql:localhost', NULL, NULL, NULL, 'CustomDriver');
		$this->dbconfig1 = new \lib360\db\connection\Config('mysql:localhost');
		$this->dbconfig2 = new \lib360\db\connection\Config('pgsql:localhost');
	}

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers lib360\db\connection\Connection::__construct
	*/
	public function testConstruct_BadDSN()
	{
		$e = NULL;
		try
		{
			$c = new HelperConnection1($this->dbconfigBad1);
		}
		catch (\lib360\db\connection\ConfigException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\connection\ConfigException', $e, "Failed to throw a \lib360\db\connection\ConfigException on invalid DSN during instantiation");
	}

	/**
	*	@covers lib360\db\connection\Connection::__construct
	*/
	public function testConstruct_BadDSNDriver()
	{
		$e = NULL;
		try
		{
			$c = new HelperConnection1($this->dbconfigBad2);
		}
		catch (\lib360\db\object\NotFoundException $e)
		{
		}
		$this->assertInstanceOf('\lib360\db\object\NotFoundException', $e, "Failed to throw a \lib360\db\object\NotFoundException on invalid driver name in DSN during instantiation");
	}

	/**
	*	@covers lib360\db\connection\Connection::__construct
	*/
	public function testConstruct_Success()
	{
		$e = NULL;
		try
		{
			$c = new HelperConnection1($this->dbconfig1);
		}
		catch (\lib360\db\object\NotFoundException $e)
		{
		}
		$this->assertEquals(NULL, $e, "Caught an exception during connection class instantiation");
	}

	/**
	*	@covers lib360\db\connection\Connection::__construct
	*	@depends testConstruct_Success
	*/
	public function testConstruct_SuccessCustomDriver()
	{
		$driverTypeBackup = \lib360\db\object\Factory::getType('Driver');
		\lib360\db\object\Factory::setType('Driver', '\lib360\tests\db\connection\\', '\lib360\db\driver\IDriver');
		$c = new HelperConnection1($this->dbconfigCustomDriver);
		$this->assertInstanceOf('\lib360\tests\db\connection\CustomDriver', $c->driver, "Connection driver is not an instance of custom driver class specified");
		\lib360\db\object\Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
	}

	/**
	*	@covers lib360\db\connection\Connection::isConnected
	*	@depends testConstruct_Success
	*/
	public function testIsConnected_False()
	{
		$c = new HelperConnection1($this->dbconfig1);
		$this->assertFalse($c->isConnected(), "Connection object should not be connected yet");
	}

	/**
	*	@covers lib360\db\connection\Connection::isConnected
	*	@depends testConstruct_Success
	*/
	public function testIsConnected_True()
	{
		$c = new HelperConnection1($this->dbconfig1);
		$c->connect();
		$this->assertTrue($c->isConnected(), "Connection object should be connected.");
	}

	/**
	*	@covers lib360\db\connection\Connection::getConnection
	*	@depends testConstruct_Success
	*/
	public function testGetConnection()
	{
		$c = new HelperConnection1($this->dbconfig1);
		$c->connect();
		$this->assertEquals($this->getProtectedProperty($c, 'connection')->getValue($c), $c->getConnection(), "Failed to return correct connection property value");
	}

	/**
	*	@covers lib360\db\connection\Connection::disconnect
	*	@depends testConstruct_Success
	*	@depends testIsConnected_True
	*/
	public function testDisconnect()
	{
		$c = new HelperConnection1($this->dbconfig1);
		$c->connect();
		$c->disconnect();
		$this->assertFalse($c->isConnected(), "Connection failed to disconnect");
	}

}

?>