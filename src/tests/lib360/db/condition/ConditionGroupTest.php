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

namespace spoof\tests\lib360\db\condition;

use spoof\lib360\db\condition\Condition;
use spoof\lib360\db\condition\ConditionGroup;
use spoof\lib360\db\value\Value;
use spoof\tests\TestCase;

class ConditionGroupTest extends TestCase
{

    public $condition1;
    public $condition2;

    public function setUp()
    {
        $e = null;
        $v1 = new Value('test1', Value::TYPE_STRING);
        $v2 = new Value('test2', Value::TYPE_STRING);
        try {
            $this->condition1 = new Condition($v1, Condition::OPERATOR_EQUALS, $v2);
            $this->condition2 = new Condition($v2, Condition::OPERATOR_NOT_EQUALS, $v1);
        } catch (\InvalidArgumentException $e) {
        }
    }

    /**
     * @covers \spoof\lib360\db\condition\ConditionGroup::__construct
     */
    public function testConstruct_Condition()
    {
        $cg = new ConditionGroup($this->condition1);
        $this->assertEquals(
            $this->condition1,
            $cg->condition,
            "Failed to set passed initial condition during instantiation"
        );
    }

    /**
     * @covers \spoof\lib360\db\condition\ConditionGroup::__construct
     */
    public function testConstruct_Conditions()
    {
        $cg = new ConditionGroup($this->condition1);
        $this->assertEquals(
            array(),
            $cg->conditions,
            "Failed to initialize conditions property during instantiation"
        );
    }

    /**
     * @covers \spoof\lib360\db\condition\ConditionGroup::__construct
     */
    public function testConstruct_Operators()
    {
        $cg = new ConditionGroup($this->condition1);
        $this->assertEquals(
            array(),
            $cg->operators,
            "Failed to initialize operators property during instantiation"
        );
    }

    /**
     * @covers \spoof\lib360\db\condition\ConditionGroup::addCondition
     */
    public function testAddCondition_Operator()
    {
        $cg = new ConditionGroup($this->condition1);
        $cg->addCondition(ConditionGroup::OPERATOR_AND, $this->condition2);
        $this->assertEquals(
            array(ConditionGroup::OPERATOR_AND),
            $cg->operators,
            "Failed to add given operator to operators array"
        );
    }

    /**
     * @covers \spoof\lib360\db\condition\ConditionGroup::addCondition
     */
    public function testAddCondition_Condition()
    {
        $cg = new ConditionGroup($this->condition1);
        $cg->addCondition(ConditionGroup::OPERATOR_AND, $this->condition2);
        $this->assertContains(
            $this->condition2,
            $cg->conditions,
            "Failed to add given condition to conditions array"
        );
    }

}

?>
