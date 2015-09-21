<?php

namespace spoof\tests\lib360\db\value;

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

use \spoof\lib360\db\value\InvalidValueException;
use \spoof\lib360\db\value\UnknownTypeException;
use \spoof\lib360\db\value\Value;

class ValueTest extends \PHPUnit_Framework_TestCase
{

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	public function test_ConstantsUnique()
	{
		$a = array(
			Value::TYPE_NULL => 0,
			Value::TYPE_STRING => 0,
			Value::TYPE_INTEGER => 0,
			Value::TYPE_FLOAT => 0,
			Value::TYPE_BOOLEAN => 0,
			Value::TYPE_BINARY => 0,
			Value::TYPE_ARRAY => 0,
			Value::TYPE_COLUMN => 0,
			Value::TYPE_PREPARED => 0,
			Value::TYPE_FUNCTION => 0
		);
		$expected = 10;
		$actual = count($a);
		$this->assertEquals($expected, $actual, "Expected $expected unique value type constants, got $actual");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*/
	public function testConstruct_InvalidType()
	{
		$e = NULL;
		try
		{
			$v = new Value('test_value', 'invalid type');
		}
		catch (UnknownTypeException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\value\UnknownTypeException', $e, "Failed to throw UnknownTypeException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*/
	public function testConstruct_NullInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value('test_value', Value::TYPE_NULL);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\value\InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*/
	public function testConstruct_NullSuccess()
	{
		$e = NULL;
		try
		{
			$v = new Value(NULL, Value::TYPE_NULL);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(
			array(NULL, Value::TYPE_NULL),
			array($this->getProtectedProperty($v,'value')->getValue($v), $this->getProtectedProperty($v, 'type')->getValue($v)),
			"Failed to set null type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::getType
	*	@depends testConstruct_NullSuccess
	*/
	public function testGetType()
	{
		$e = NULL;
		try
		{
			$v = new Value(NULL, Value::TYPE_NULL);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(Value::TYPE_NULL, $v->getType(), "Failed to match expected result for null type");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::getValue
	*	@depends testConstruct_NullSuccess
	*/
	public function testGetValue()
	{
		$e = NULL;
		try
		{
			$v = new Value(NULL, Value::TYPE_NULL);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(NULL, $v->getValue(), "Failed to match expected result for null value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_StringInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value(123, Value::TYPE_STRING);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\value\InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_StringSuccess()
	{
		$e = NULL;
		$s = 'test string';
		try
		{
			$v = new Value($s, Value::TYPE_STRING);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_STRING), array($v->getValue(), $v->getType()), "Failed to set string type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_IntegerInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value('asdf', Value::TYPE_INTEGER);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_IntegerSuccess()
	{
		$e = NULL;
		$s = 1234;
		try
		{
			$v = new Value($s, Value::TYPE_INTEGER);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_INTEGER), array($v->getValue(), $v->getType()), "Failed to set integer type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_FloatInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value(new \stdClass(), Value::TYPE_FLOAT);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\value\InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_FloatSuccess()
	{
		$e = NULL;
		$s = 123.45;
		try
		{
			$v = new Value($s, Value::TYPE_FLOAT);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_FLOAT), array($v->getValue(), $v->getType()), "Failed to set float type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_BooleanInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value(0, Value::TYPE_BOOLEAN);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\value\InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_BooleanSuccess()
	{
		$e = NULL;
		$s = TRUE;
		try
		{
			$v = new Value($s, Value::TYPE_BOOLEAN);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_BOOLEAN), array($v->getValue(), $v->getType()), "Failed to set boolean type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_BinarySuccess()
	{
		$e = NULL;
		$s = b'Hello';
		try
		{
			$v = new Value($s, Value::TYPE_BINARY);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_BINARY), array($v->getValue(), $v->getType()), "Failed to set binary type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_ArrayInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value(0, Value::TYPE_ARRAY);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_ArraySuccess()
	{
		$e = NULL;
		$s = array(1, 2, 3);
		try
		{
			$v = new Value($s, Value::TYPE_ARRAY);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_ARRAY), array($v->getValue(), $v->getType()), "Failed to set array type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_ColumnInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value(2.3, Value::TYPE_COLUMN);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\value\InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_ColumnSuccess()
	{
		$e = NULL;
		$s = 'test';
		try
		{
			$v = new Value($s, Value::TYPE_COLUMN);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_COLUMN), array($v->getValue(), $v->getType()), "Failed to set column type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_PreparedInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value(0, Value::TYPE_PREPARED);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_PreparedSuccess()
	{
		$e = NULL;
		$s = 'asdf';
		try
		{
			$v = new Value($s, Value::TYPE_PREPARED);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_PREPARED), array($v->getValue(), $v->getType()), "Failed to set prepared type and value");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_FunctionInvalid()
	{
		$e = NULL;
		try
		{
			$v = new Value(0, Value::TYPE_FUNCTION);
		}
		catch (InvalidValueException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\value\InvalidValueException', $e, "Failed to throw InvalidValueException when invalid value type given");
	}

	/**
	*	@covers \spoof\lib360\db\value\Value::__construct
	*	@depends testGetType
	*	@depends testGetValue
	*/
	public function testConstruct_FunctionSuccess()
	{
		$e = NULL;
		$s = array('function', 'arg1', 'arg2');
		try
		{
			$v = new Value($s, Value::TYPE_FUNCTION);
		}
		catch (InvalidValueException $e)
		{
			$this->fail("Got an exception of type " . get_class($e));
		}
		$this->assertEquals(array($s, Value::TYPE_FUNCTION), array($v->getValue(), $v->getType()), "Failed to set function type and value");
	}

}

?>
