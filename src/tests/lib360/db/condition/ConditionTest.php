<?php

namespace spoof\tests\lib360\db\condition;

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

use \spoof\lib360\db\condition\Condition;
use \spoof\lib360\db\value\Value;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_InvalidValue2OperatorIn()
	{
		$e = NULL;
		$value1 = new Value('test1', Value::TYPE_STRING);
		$value2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$condition = new Condition($value1, Condition::OPERATOR_IN, $value2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Condition failed to throw exception when instantiated with IN operator and non-array second value");
	}

	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_InvalidValue2OperatorNotIn()
	{
		$e = NULL;
		$value1 = new Value('test1', Value::TYPE_STRING);
		$value2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$condition = new Condition($value1, Condition::OPERATOR_NOT_IN, $value2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Condition failed to throw exception when instantiated with NOT IN operator and non-array second value");
	}

	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_SuccessNoException()
	{
		$e = NULL;
		$value1 = new Value('test1', Value::TYPE_STRING);
		$value2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$condition = new Condition($value1, Condition::OPERATOR_EQUALS, $value2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals(NULL, $e, "Threw an exception when none should have occurred");
	}

	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_SuccessValue1()
	{
		$e = NULL;
		$value1 = new Value('test1', Value::TYPE_STRING);
		$value2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$condition = new Condition($value1, Condition::OPERATOR_EQUALS, $value2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals($value1, $condition->value1, "Threw an exception when none should have occurred");
	}

	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_SuccessValue2()
	{
		$e = NULL;
		$value1 = new Value('test1', Value::TYPE_STRING);
		$value2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$condition = new Condition($value1, Condition::OPERATOR_EQUALS, $value2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals($value2, $condition->value2, "Threw an exception when none should have occurred");
	}

	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_SuccessOperator()
	{
		$e = NULL;
		$value1 = new Value('test1', Value::TYPE_STRING);
		$value2 = new Value('test2', Value::TYPE_STRING);
		$operator = Condition::OPERATOR_EQUALS;
		try
		{
			$condition = new Condition($value1, $operator, $value2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals($operator, $condition->operator, "Threw an exception when none should have occurred");
	}

}

?>
