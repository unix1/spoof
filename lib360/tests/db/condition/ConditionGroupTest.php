<?php

namespace lib360\db\tests\condition;

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

class ConditionGroupTest extends \PHPUnit_Framework_TestCase
{

	public $condition1;
	public $condition2;

	public function setUp()
	{
		$e = NULL;
		$v1 = new \lib360\db\value\Value('test1', \lib360\db\value\Value::TYPE_STRING);
		$v2 = new \lib360\db\value\Value('test2', \lib360\db\value\Value::TYPE_STRING);
		try
		{
			$this->condition1 = new \lib360\db\condition\Condition($v1, \lib360\db\condition\Condition::OPERATOR_EQUALS, $v2);
			$this->condition2 = new \lib360\db\condition\Condition($v2, \lib360\db\condition\Condition::OPERATOR_NOT_EQUALS, $v1);
		}
		catch (\InvalidArgumentException $e)
		{
		}
	}

	/**
	*	@covers \lib360\db\condition\ConditionGroup::__construct
	*/
	public function testConstruct_Condition()
	{
		$cg = new \lib360\db\condition\ConditionGroup($this->condition1);
		$this->assertEquals($this->condition1, $cg->condition, "Failed to set passed initial condition during instantiation");
	}

	/**
	*	@covers \lib360\db\condition\ConditionGroup::__construct
	*/
	public function testConstruct_Conditions()
	{
		$cg = new \lib360\db\condition\ConditionGroup($this->condition1);
		$this->assertEquals(array(), $cg->conditions, "Failed to initialize conditions property during instantiation");
	}

	/**
	*	@covers \lib360\db\condition\ConditionGroup::__construct
	*/
	public function testConstruct_Operators()
	{
		$cg = new \lib360\db\condition\ConditionGroup($this->condition1);
		$this->assertEquals(array(), $cg->operators, "Failed to initialize operators property during instantiation");
	}

	/**
	*	@covers \lib360\db\condition\ConditionGroup::addCondition
	*/
	public function testAddCondition_Operator()
	{
		$cg = new \lib360\db\condition\ConditionGroup($this->condition1);
		$cg->addCondition(\lib360\db\condition\ConditionGroup::OPERATOR_AND, $this->condition2);
		$this->assertEquals(array(\lib360\db\condition\ConditionGroup::OPERATOR_AND), $cg->operators, "Failed to add given operator to operators array");
	}

	/**
	*	@covers \lib360\db\condition\ConditionGroup::addCondition
	*/
	public function testAddCondition_Condition()
	{
		$cg = new \lib360\db\condition\ConditionGroup($this->condition1);
		$cg->addCondition(\lib360\db\condition\ConditionGroup::OPERATOR_AND, $this->condition2);
		$this->assertContains($this->condition2, $cg->conditions, "Failed to add given condition to conditions array");
	}

}

?>