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

use spoof\lib360\db\connection\Config;
use spoof\lib360\db\connection\Pool;
use spoof\lib360\db\object\Factory;
use spoof\tests\lib360\db\connection\HelperConnection1;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    protected $s;
    protected $s2;
    protected $c;
    protected $d;
    protected $driverTypeBackup;

    public function setUp()
    {
        $this->s = new HelperStore();
        $this->s2 = new HelperStoreCustom();
        $this->driverTypeBackup = Factory::getType('Driver');
        Factory::setType('Driver', '\spoof\tests\lib360\db\data\\', '\spoof\lib360\db\driver\IDriver');
        $this->c = new HelperConnection1(new Config('mysql:localhost', null, null, null, 'CustomDriver'));
        $this->d = new CustomDriver();
        Pool::add($this->c, $this->s->getDB());
    }

    public function tearDown()
    {
        Factory::setType('Driver', $this->driverTypeBackup['base'], $this->driverTypeBackup['interface']);
        Pool::removeByName($this->s->getDB());
    }

    /**
     * @covers \spoof\lib360\db\data\Store::getName
     */
    public function testGetName()
    {
        $actual = $this->s->getName();
        $this->assertEquals(
            $this->getProtectedProperty($this->s, 'name')->getValue($this->s),
            $actual,
            "getName didn't return the value of name property from extending class"
        );
    }

    protected function getProtectedProperty($class, $property)
    {
        $r = new \ReflectionClass($class);
        $p = $r->getProperty($property);
        $p->setAccessible(true);
        return $p;
    }

    /**
     * @covers \spoof\lib360\db\data\Store::getDB
     */
    public function testGetDB()
    {
        $actual = $this->s->getDB();
        $this->assertEquals(
            $this->getProtectedProperty($this->s, 'db')->getValue($this->s),
            $actual,
            "getName didn't return the value of name property from extending class"
        );
    }

    /**
     * @covers  \spoof\lib360\db\data\Store::setName
     * @depends testGetName
     */
    public function testSetName()
    {
        $name = 'new_name';
        $oldName = $this->s->getName();
        $this->s->setName($name);
        $this->assertEquals($name, $this->s->getName(), "Failed to set new storage name via setName");
        $this->s->setName($oldName);
    }

    /**
     * @covers  \spoof\lib360\db\data\Store::setDB
     * @depends testGetDB
     */
    public function testSetDB()
    {
        $db = 'new_db';
        $oldDB = $this->s->getDB();
        $this->s->setDB($db);
        $this->assertEquals($db, $this->s->getDB(), "Failed to set new database connection name via setName");
        $this->s->setDB($oldDB);
    }

    /**
     * @covers \spoof\lib360\db\data\Store::getExecutor
     */
    public function testGetExecutor_Default()
    {
        $this->assertEquals(
            $this->d->executor,
            $this->s->getExecutor(),
            "Returned executor didn't match default driver executor"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Store::getLanguage
     */
    public function testGetLanguage_Default()
    {
        $this->assertEquals(
            $this->d->language,
            $this->s->getLanguage(),
            "Returned language didn't match default driver language"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Store::getExecutor
     */
    public function testGetExecutor_Custom()
    {
        $this->assertEquals(
            $this->getProtectedProperty($this->s2, 'executor')->getValue($this->s2),
            $this->s2->getExecutor(), "Returned executor didn't match local custom executor specified"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Store::getLanguage
     */
    public function testGetLanguage_Custom()
    {
        $this->assertEquals(
            $this->getProtectedProperty($this->s2, 'language')->getValue($this->s2),
            $this->s2->getLanguage(), "Returned language didn't match local custom language specified"
        );
    }

}

?>
