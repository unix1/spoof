<?php

namespace lib360\tests\db\data;

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

class TableFactoryTest extends \PHPUnit_Framework_TestCase
{

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers \lib360\db\data\TableFactory::cache
	*/
	public function testCache()
	{
		$t1 = new HelperTable();
		\lib360\db\data\TableFactory::cache($t1);
		$p = $this->getProtectedProperty('\lib360\db\data\TableFactory', 'cache')->getValue();
		$t2 = $p[$t1->getDB()][$t1->getName()];
		$this->assertEquals($t1, $t2, "Cached value is different from the returned value");
	}

	/**
	*	@covers \lib360\db\data\TableFactory::get
	*	@depends testCache
	*/
	public function testGet()
	{
		$t = \lib360\db\data\TableFactory::get('test_connection', 'test_table');
		$this->assertInstanceOf('\lib360\db\data\Table', $t, "Result was not an instance of \lib360\db\data\Table");
	}

}

?>