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

namespace spoof\tests\lib360\db\join;

use spoof\lib360\db\condition\Condition;
use spoof\lib360\db\join\Join;
use spoof\lib360\db\value\Value;

class JoinTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \spoof\lib360\db\join\Join::__construct
     */
    public function testConstruct()
    {
        $t1_name = 'table1';
        $t2_name = 'table2';
        $join = Join::JOIN_TYPE_INNER;
        $cond = new Condition(
            new Value('test1value', Value::TYPE_COLUMN),
            Condition::OPERATOR_EQUALS,
            new Value('test2value', Value::TYPE_STRING)
        );
        $j = new Join($t1_name, $join, $t2_name, $cond);
        $this->assertEquals(
            array($t1_name, array($join), array($t2_name), array($cond)),
            array($j->table_base, $j->type, $j->table_join, $j->condition),
            "Constructor failed to set given values to join object properties"
        );
    }

    /**
     * @covers  \spoof\lib360\db\join\Join::addTable
     * @depends testConstruct
     */
    public function testAddTable()
    {
        $t1_name = 'table1';
        $t2_name = 'table2';
        $t3_name = 'table3';
        $join12 = Join::JOIN_TYPE_INNER;
        $join23 = Join::JOIN_TYPE_LEFT_OUTER;
        $cond12 = new Condition(
            new Value('test1value', Value::TYPE_COLUMN),
            Condition::OPERATOR_EQUALS,
            new Value('test2value', Value::TYPE_STRING)
        );
        $cond23 = new Condition(
            new Value('test3value', Value::TYPE_COLUMN),
            Condition::OPERATOR_NOT_EQUALS,
            new Value('test4value', Value::TYPE_COLUMN)
        );
        $j = new Join($t1_name, $join12, $t2_name, $cond12);
        $j->addTable($join23, $t3_name, $cond23);
        $this->assertEquals(
            array($t1_name, array($join12, $join23), array($t2_name, $t3_name), array($cond12, $cond23)),
            array($j->table_base, $j->type, $j->table_join, $j->condition),
            "Failed to set given values to join object properties"
        );
    }

}

?>
