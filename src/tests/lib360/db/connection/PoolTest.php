<?php

namespace spoof\tests\lib360\db\connection;

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

use \spoof\lib360\db\connection\Config;
use \spoof\lib360\db\connection\Pool;

class PoolTest extends \PHPUnit_Framework_TestCase
{
	public $conn1;
	public $conn2;

	public function setUp()
	{
		$this->conn1 = new HelperConnection1(new Config('mysql:localhost'));
		$this->conn2 = new HelperConnection1(new Config('mysql:localhost'));
	}

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::add
	*/
	public function testAdd_SuccessNoException()
	{
		$e = NULL;
		try
		{
			Pool::add($this->conn1, 'test1');
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals(NULL, $e, "Caught an exception when adding a unique element");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::add
	*	@depends testAdd_SuccessNoException
	*/
	public function testAdd_SuccessElementKey()
	{
		$e = NULL;
		try
		{
			Pool::add($this->conn2, 'test2');
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertArrayHasKey('test2', $this->getProtectedProperty('\spoof\lib360\db\connection\Pool', 'connections')->getValue(), "Failed to create an internal array key with given label");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::add
	*	@depends testAdd_SuccessElementKey
	*/
	public function testAdd_SuccessElementValue()
	{
		$c = $this->getProtectedProperty('\spoof\lib360\db\connection\Pool', 'connections')->getValue();
		$this->assertEquals($this->conn2, $c['test2'], "Connection object doesn't match");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::add
	*	@depends testAdd_SuccessElementKey
	*/
	public function testAdd_FailDuplicate()
	{
		$e = NULL;
		try
		{
			Pool::add($this->conn2, 'test1');
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Failed to throw exception on duplicate key");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::getByName
	*	@depends testAdd_SuccessElementKey
	*/
	public function testGetByName_FailNotFound()
	{
		$e = NULL;
		try
		{
			Pool::getByName('test', FALSE);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Failed to throw exception when retrieving a non-existent key");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::getByName
	*	@depends testAdd_SuccessElementValue
	*/
	public function testGetByName_SuccessNoException()
	{
		$e = NULL;
		try
		{
			Pool::getByName('test1', FALSE);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals(NULL, $e, "Got an exception while retrieving a valid connection key");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::getByName
	*	@depends testAdd_SuccessElementValue
	*/
	public function testGetByName_SuccessObject()
	{
		$e = NULL;
		$c = NULL;
		try
		{
			$c = Pool::getByName('test1', FALSE);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals($this->conn1, $c, "Object returned from getByName doesn't match the object that had been set");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::getByName
	*	@depends testGetByName_SuccessObject
	*/
	public function testGetByName_SuccessNoConnection()
	{
		$e = NULL;
		$c = NULL;
		try
		{
			$c = Pool::getByName('test1', FALSE);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals(FALSE, $c->isConnected(), "The returned connection object was in a connected state");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::getByName
	*	@depends testGetByName_SuccessNoConnection
	*/
	public function testGetByName_SuccessWithConnect()
	{
		$e = NULL;
		$c = NULL;
		try
		{
			$c = Pool::getByName('test1', TRUE);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals(TRUE, $c->isConnected(), "The returned connection object was not in a connected state");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::removeByName
	*	@depends testGetByName_SuccessNoConnection
	*/
	public function testRemoveByName_FailNotFound()
	{
		$e = NULL;
		try
		{
			Pool::removeByName('testconnection_that_doesnt_exist');
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Failed to throw an exception when invalid connection name was given to be removed");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::removeByName
	*	@depends testGetByName_SuccessWithConnect
	*/
	public function testRemoveByName_SuccessNoDisconnectCheckConnection()
	{
		try
		{
			$c = Pool::getByName('test1');
			Pool::removeByName('test1', FALSE);
		}
		catch (\InvalidArgumentException $e)
		{
			$this->fail("Got an exception when attempting to remove an existing connection from pool");
		}
		$this->assertEquals(TRUE, $c->isConnected(), "Connection was disconnected during removal when told not to disconnect");
	}


	/**
	*	@covers \spoof\lib360\db\connection\Pool::removeByName
	*	@depends testGetByName_SuccessWithConnect
	*/
	public function testRemoveByName_SuccessDisconnectCheckConnection()
	{
		try
		{
			$c = Pool::getByName('test2');
			Pool::removeByName('test2', TRUE);
		}
		catch (\InvalidArgumentException $e)
		{
			$this->fail("Got an exception when attempting to remove an existing connection from pool");
		}
		$this->assertEquals(FALSE, $c->isConnected(), "Connection was not disconnected during removal when told to disconnect");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::removeByName
	*	@depends testRemoveByName_SuccessNoDisconnectCheckConnection
	*	@depends testRemoveByName_SuccessDisconnectCheckConnection
	*/
	public function testRemoveByName_SuccessCheckElement()
	{
		Pool::add($this->conn1, 'test1');
		Pool::removeByName('test1');
		$c = $this->getProtectedProperty('\spoof\lib360\db\connection\Pool', 'connections')->getValue();
		$this->assertEquals(FALSE, isset($c['test1']), "Failed to unset internal reference to connection object");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Pool::closeAll
	*	@depends testGetByName_SuccessObject
	*/
	public function testCloseAll()
	{
		$e = NULL;
		$c1 = NULL;
		$c2 = NULL;
		Pool::add($this->conn1, 'test1');
		Pool::add($this->conn2, 'test2');
		try
		{
			$c1 = Pool::getByName('test1', FALSE);
			$c2 = Pool::getByName('test2', FALSE);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$c1->connect();
		$c2->connect();
		Pool::closeAll();
		$this->assertFalse($c1->isConnected() || $c2->isConnected(), "At least one connection was left connected");
		Pool::removeByName('test1');
		Pool::removeByName('test2');
	}

}

?>
