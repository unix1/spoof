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

namespace spoof\tests\lib360\db\data;

use spoof\lib360\db\data\TableFactory;
use spoof\tests\TestCase;
use spoof\tests\Util;

class TableFactoryTest extends TestCase
{

    /**
     * @covers \spoof\lib360\db\data\TableFactory::cache
     */
    public function testCache()
    {
        $t1 = new HelperTable();
        TableFactory::cache($t1);
        $p = Util::getProtectedProperty('\spoof\lib360\db\data\TableFactory', 'cache');
        $t2 = $p[$t1->getDB()][$t1->getName()];
        $this->assertEquals($t1, $t2, "Cached value is different from the returned value");
    }

    /**
     * @covers  \spoof\lib360\db\data\TableFactory::get
     * @depends testCache
     */
    public function testGet()
    {
        $t = TableFactory::get('test_connection', 'test_table');
        $this->assertInstanceOf(
            '\spoof\lib360\db\data\Table',
            $t,
            "Result was not an instance of \spoof\lib360\db\data\Table"
        );
    }

}

?>
