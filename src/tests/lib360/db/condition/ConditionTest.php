<?php

namespace spoof\tests\lib360\db\condition;

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

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'lib360' . DIRECTORY_SEPARATOR . 'initialize.php');

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
		$v1 = new Value('test1', Value::TYPE_STRING);
		$v2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$o = new Condition($v1, Condition::OPERATOR_IN, $v2);
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
		$v1 = new Value('test1', Value::TYPE_STRING);
		$v2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$o = new Condition($v1, Condition::OPERATOR_NOT_IN, $v2);
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
		$v1 = new Value('test1', Value::TYPE_STRING);
		$v2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$o = new Condition($v1, Condition::OPERATOR_EQUALS, $v2);
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
		$v1 = new Value('test1', Value::TYPE_STRING);
		$v2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$o = new Condition($v1, Condition::OPERATOR_EQUALS, $v2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals($v1, $o->value1, "Threw an exception when none should have occurred");
	}

	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_SuccessValue2()
	{
		$e = NULL;
		$v1 = new Value('test1', Value::TYPE_STRING);
		$v2 = new Value('test2', Value::TYPE_STRING);
		try
		{
			$o = new Condition($v1, Condition::OPERATOR_EQUALS, $v2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals($v2, $o->value2, "Threw an exception when none should have occurred");
	}

	/**
	*	@covers \spoof\lib360\db\condition\Condition::__construct
	*/
	public function testConstruct_SuccessOperator()
	{
		$e = NULL;
		$v1 = new Value('test1', Value::TYPE_STRING);
		$v2 = new Value('test2', Value::TYPE_STRING);
		$operator = Condition::OPERATOR_EQUALS;
		try
		{
			$o = new Condition($v1, $operator, $v2);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertEquals($operator, $o->operator, "Threw an exception when none should have occurred");
	}

}

?>
