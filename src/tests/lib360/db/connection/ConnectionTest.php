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

namespace spoof\tests\lib360\db\connection;

use spoof\lib360\db\connection\Config;
use spoof\lib360\db\connection\ConfigException;
use spoof\lib360\db\object\Factory;
use spoof\lib360\db\object\NotFoundException;
use spoof\tests\TestCase;

class ConnectionTest extends TestCase
{
    public $configBad1;
    public $configBad2;
    public $config1;
    public $config2;
    public $configCustomDriver;

    public function setUp()
    {
        $this->configBad1 = new Config('mysql');
        $this->configBad2 = new Config('fake:localhost');
        $this->configCustomDriver = new Config('mysql:localhost', null, null, null, 'CustomDriver');
        $this->config1 = new Config('mysql:localhost');
        $this->config2 = new Config('pgsql:localhost');
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::__construct
     * @covers \spoof\lib360\db\connection\Connection::parseDriver
     */
    public function testConstruct_BadDSN()
    {
        $e = null;
        try {
            $c = new HelperConnection1($this->configBad1);
        } catch (ConfigException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\connection\ConfigException',
            $e,
            "Failed to throw a \spoof\lib360\db\connection\ConfigException on invalid DSN during instantiation"
        );
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::__construct
     * @covers \spoof\lib360\db\connection\Connection::parseDriver
     */
    public function testConstruct_BadDSNDriver()
    {
        $e = null;
        try {
            $c = new HelperConnection1($this->configBad2);
        } catch (NotFoundException $e) {
        }
        $this->assertInstanceOf(
            '\spoof\lib360\db\object\NotFoundException',
            $e,
            "Failed to throw a \spoof\lib360\db\object\NotFoundException on invalid driver name in DSN during instantiation"
        );
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::__construct
     * @covers \spoof\lib360\db\connection\Connection::parseDriver
     */
    public function testConstruct_Success()
    {
        $e = null;
        try {
            $c = new HelperConnection1($this->config1);
        } catch (NotFoundException $e) {
        }
        $this->assertEquals(null, $e, "Caught an exception during connection class instantiation");
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::__construct
     * @covers \spoof\lib360\db\connection\Connection::parseDriver
     * @depends testConstruct_Success
     */
    public function testConstruct_SuccessCustomDriver()
    {
        $driverTypeBackup = Factory::getType('Driver');
        Factory::setType('Driver', '\spoof\tests\lib360\db\connection\\', '\spoof\lib360\db\driver\IDriver');
        $c = new HelperConnection1($this->configCustomDriver);
        $this->assertInstanceOf(
            '\spoof\tests\lib360\db\connection\CustomDriver',
            $c->driver,
            "Connection driver is not an instance of custom driver class specified"
        );
        Factory::setType('Driver', $driverTypeBackup['base'], $driverTypeBackup['interface']);
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::isConnected
     * @depends testConstruct_Success
     */
    public function testIsConnected_False()
    {
        $c = new HelperConnection1($this->config1);
        $this->assertFalse($c->isConnected(), "Connection object should not be connected yet");
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::isConnected
     * @depends testConstruct_Success
     */
    public function testIsConnected_True()
    {
        $c = new HelperConnection1($this->config1);
        $c->connect();
        $this->assertTrue($c->isConnected(), "Connection object should be connected.");
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::getConnection
     * @depends testConstruct_Success
     */
    public function testGetConnection()
    {
        $c = new HelperConnection1($this->config1);
        $c->connect();
        $this->assertEquals(
            $this->getProtectedProperty($c, 'connection'),
            $c->getConnection(),
            "Failed to return correct connection property value"
        );
    }

    /**
     * @covers \spoof\lib360\db\connection\Connection::disconnect
     * @depends testConstruct_Success
     * @depends testIsConnected_True
     */
    public function testDisconnect()
    {
        $c = new HelperConnection1($this->config1);
        $c->connect();
        $c->disconnect();
        $this->assertFalse($c->isConnected(), "Connection failed to disconnect");
    }

}

?>
