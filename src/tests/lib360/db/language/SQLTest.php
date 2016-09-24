<?php

namespace spoof\tests\lib360\db\language;

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
use \spoof\lib360\db\condition\ConditionGroup;
use \spoof\lib360\db\join\Join;
use \spoof\lib360\db\join\UnknownTypeException;
use \spoof\lib360\db\language\SQL;
use \spoof\lib360\db\language\SQLException;
use \spoof\lib360\db\query\Query;
use \spoof\lib360\db\value\Value;

class SQLTest extends \PHPUnit_Framework_TestCase
{

	public $l;
	public $d;

	public function setUp()
	{
		$this->l = new SQL();
		$this->d = new HelperDriver();
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionGroupOperator
	*/
	public function testGetConditionGroupOperator_And()
	{
		$v = $this->l->getConditionGroupOperator($this->d, ConditionGroup::OPERATOR_AND);
		$this->assertEquals(SQL::CONDITIONGROUP_AND, $v, "Failed to return AND operator value");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionGroupOperator
	*/
	public function testGetConditionGroupOperator_Or()
	{
		$v = $this->l->getConditionGroupOperator($this->d, ConditionGroup::OPERATOR_OR);
		$this->assertEquals(SQL::CONDITIONGROUP_OR, $v, "Failed to return AND operator value");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionGroupOperator
	*/
	public function testGetConditionGroupOperator_Invalid()
	{
		$e = NULL;
		try
		{
			$v = $this->l->getConditionGroupOperator($this->d, 'asdf');
		}
		catch (SQLException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\language\SQLException', $e, "Failed to throw exception when invalid condition group operator specified");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_Equals()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(3.02, Value::TYPE_FLOAT)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_EQUALS, $v, "Failed to return SQL 'equals' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_EqualsNull()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_EQUALS_NULL, $v, "Failed to return SQL 'is/equals null' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_NotEquals()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_NOT_EQUALS,
			new Value(3.02, Value::TYPE_FLOAT)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_NOT_EQUALS, $v, "Failed to return SQL 'not equals' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_NotEqualsNull()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_NOT_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_NOT_EQUALS_NULL, $v, "Failed to return SQL 'is not/not equals null' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_Greater()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_GREATER_THAN,
			new Value(5, Value::TYPE_INTEGER)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_GREATER_THAN, $v, "Failed to return SQL 'greater than' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_GreaterOrEqual()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_GREATER_THAN_OR_EQUAL,
			new Value(5, Value::TYPE_INTEGER)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_GREATER_THAN_OR_EQUAL, $v, "Failed to return SQL 'greater than or equals' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_Less()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_LESS_THAN,
			new Value(5, Value::TYPE_INTEGER)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_LESS_THAN, $v, "Failed to return SQL 'less than' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_LessOrEqual()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_LESS_THAN_OR_EQUAL,
			new Value(5, Value::TYPE_INTEGER)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_LESS_THAN_OR_EQUAL, $v, "Failed to return SQL 'less than or equals' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_In()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_IN,
			new Value(array(1, 2, 3), Value::TYPE_ARRAY)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_IN, $v, "Failed to return SQL 'in' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_NotIn()
	{
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			Condition::OPERATOR_NOT_IN,
			new Value(array(1, 2, 3), Value::TYPE_ARRAY)
		);
		$v = $this->l->getConditionOperator($this->d, $c);
		$this->assertEquals(SQL::CONDITION_NOT_IN, $v, "Failed to return SQL 'not in' operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getConditionOperator
	*/
	public function testGetConditionOperator_Invalid()
	{
		$e = NULL;
		$c = new Condition(
			new Value('test', Value::TYPE_COLUMN),
			'asdfasdf',
			new Value(array(1, 2, 3), Value::TYPE_ARRAY)
		);
		try
		{
			$v = $this->l->getConditionOperator($this->d, $c);
		}
		catch (SQLException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\language\SQLException', $e, "Failed to throw an exception for invalid operator");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getFieldFormatted
	*/
	public function testGetFieldFormatted()
	{
		$field = 'test_tablespace.test_table.test_field';
		$separator = $this->d->column_quote_end . $this->d->table_column_separator . $this->d->column_quote_start;
		$fieldFormattedExpected = $this->d->column_quote_start . 'test_tablespace' . $separator . 'test_table' . $separator . 'test_field' . $this->d->column_quote_end;
		$fieldFormattedActual = $this->l->getFieldFormatted($this->d, $field);
		$this->assertEquals($fieldFormattedExpected, $fieldFormattedActual, "Failed to format field to driver specifications");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getValue
	*/
	public function testGetValue_Prepared()
	{
		$vString = 'prepared_value';
		$v = new Value($vString, Value::TYPE_PREPARED);
		$queryActual = $this->l->getValue($this->d, $v);
		$queryExpected = new Query();
		$queryExpected->addString(SQL::BIND_CHAR . $vString);
		$this->assertEquals($queryExpected, $queryActual, "Failed to return query object containing prepared value");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getValue
	*	@depends testGetFieldFormatted
	*/
	public function testGetValue_Column()
	{
		$vString = 'testdb.testtablespace.tableA.columnB';
		$v = new Value($vString, Value::TYPE_COLUMN);
		$queryActual = $this->l->getValue($this->d, $v);
		$queryExpected = new Query();
		$queryExpected->addString($this->l->getFieldFormatted($this->d, $v->getValue()));
		$this->assertEquals($queryExpected, $queryActual, "Failed to return query object containing formatted column");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getValue
	*/
	public function testGetValue_Null()
	{
		$v = new Value(NULL, Value::TYPE_NULL);
		$queryActual = $this->l->getValue($this->d, $v);
		$queryExpected = new Query();
		$queryExpected->addString(SQL::VALUE_NULL);
		$this->assertEquals($queryExpected, $queryActual, "Failed to return query object containing formatted column");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getValue
	*/
	public function testGetValue_Array()
	{
		$v1 = new Value('test1', Value::TYPE_PREPARED);
		$v2 = new Value('test2', Value::TYPE_PREPARED);
		$v3 = new Value('table3.test3', Value::TYPE_COLUMN);
		$v = new Value(array($v1, $v2, $v3), Value::TYPE_ARRAY);
		$queryActual = $this->l->getValue($this->d, $v);
		$queryExpected = new Query();
		$expectedString = SQL::CONDITION_VALUES_WRAPPER_START .
			' ' . SQL::BIND_CHAR . $v1->getValue() .
			SQL::CONDITION_VALUES_SEPARATOR .
			' ' . SQL::BIND_CHAR . $v2->getValue() .
			SQL::CONDITION_VALUES_SEPARATOR .
			' ' . $this->l->getFieldFormatted($this->d, $v3->getValue()) .
			' ' . SQL::CONDITION_VALUES_WRAPPER_END;
		$queryExpected->addString($expectedString);
		$this->assertEquals($queryExpected, $queryActual, "Failed to return query object containing formatted column");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getValue
	*/
	public function testGetValue_StringTag()
	{
		$vString = 'test string';
		$v = new Value($vString, Value::TYPE_STRING);
		$query = $this->l->getValue($this->d, $v);
		$key = substr($query->query, 1);
		$this->assertArrayHasKey($key, $query->values, "Query object did not have value array element with matching tag");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getValue
	*	@depends testGetValue_StringTag
	*/
	public function testGetValue_StringValue()
	{
		$vString = 'test string';
		$v = new Value($vString, Value::TYPE_STRING);
		$query = $this->l->getValue($this->d, $v);
		$key = substr($query->query, 1);
		$this->assertEquals($v, $query->values[$key], "Query object did not have matching value object in corresponding array index");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getCondition
	*	@depends testGetValue_Column
	*	@depends testGetValue_Null
	*	@depends testGetConditionOperator_Equals
	*/
	public function testGetCondition_Condition()
	{
		$c = new Condition(
			new Value('testcolumn1', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$actual = $this->l->getCondition($this->d, $c);
		$expected = $this->l->getValue($this->d, $c->value1);
		$expected->addString($this->l->getConditionOperator($this->d, $c));
		$expected->addQuery($this->l->getValue($this->d, $c->value2));
		$this->assertEquals($expected, $actual, "Condition query object didn't match the expected value");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getCondition
	*	@depends testGetValue_Column
	*	@depends testGetValue_Null
	*	@depends testGetConditionOperator_Equals
	*	@depends testGetCondition_Condition
	*/
	public function testGetCondition_ConditionGroup()
	{
		$c1 = new Condition(
			new Value('testcolumn1', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$o = ConditionGroup::OPERATOR_AND;
		$c2 = new Condition(
			new Value('testcolumn1', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$c = new ConditionGroup($c1);
		$c->addCondition($o, $c2);
		$actual = $this->l->getCondition($this->d, $c);
		$expected = new Query();
		$expected->setString(SQL::CONDITION_WRAPPER_START);
		$expected->addQuery($this->l->getCondition($this->d, $c1));
		$expected->addString($this->l->getConditionGroupOperator($this->d, $o));
		$expected->addQuery($this->l->getCondition($this->d, $c2));
		$expected->addString(SQL::CONDITION_WRAPPER_END);
		$this->assertEquals($expected, $actual, "Condition query object didn't match the expected value");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFieldFormatted
	*	@depends testGetFieldFormatted
	*/
	public function testGetSelectFieldFormatted_WithoutAs()
	{
		$field = 'tablespace.table.field';
		$actual = $this->l->getSelectFieldFormatted($this->d, 0, $field);
		$expected = $this->l->getFieldFormatted($this->d, $field);
		$this->assertEquals($expected, $actual, "Field didn't get formatted correctly for select");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFieldFormatted
	*	@depends testGetFieldFormatted
	*/
	public function testGetSelectFieldFormatted_WithAs()
	{
		$field = 'tablespace.table.field';
		$field_as = 'test_field';
		$actual = $this->l->getSelectFieldFormatted($this->d, $field, $field_as);
		$expected = $this->l->getFieldFormatted($this->d, $field) . ' ' . SQL::SELECT_AS . ' ' . $this->d->column_quote_start . $field_as . $this->d->column_quote_end;
		$this->assertEquals($expected, $actual, "Field didn't get formatted correctly for select as");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFields
	*	@depends testGetSelectFieldFormatted_WithoutAs
	*	@depends testGetSelectFieldFormatted_WithAs
	*/
	public function testGetSelectFields()
	{
		$field1key = 'user.id';
		$field1value = 'user_id';
		$field2key = 1;
		$field2value = 'name';
		$fields = array($field1key => $field1value, $field2key => $field2value);
		$actual = $this->l->getSelectFields($this->d, $fields);
		$expected = $this->l->getSelectFieldFormatted($this->d, $field1key, $field1value) .
					SQL::SELECT_FIELD_SEPARATOR .
					$this->l->getSelectFieldFormatted($this->d, $field2key, $field2value);
		$this->assertEquals($expected, $actual, "Select fields string didn't match expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFields
	*/
	public function testGetSelectFields_AllArray()
	{
		$fields = array();
		$actual = $this->l->getSelectFields($this->d, $fields);
		$expected = SQL::SELECT_FIELDS_ALL;
		$this->assertEquals($expected, $actual, "Select all fields string didn't match expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFields
	*/
	public function testGetSelectFields_AllNull()
	{
		$actual = $this->l->getSelectFields($this->d);
		$expected = SQL::SELECT_FIELDS_ALL;
		$this->assertEquals($expected, $actual, "Select all fields string didn't match expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFromTableName
	*/
	public function testGetSelectFromTableName()
	{
		$table = 'test_table';
		$actual = $this->l->getSelectFromTableName($this->d, $table);
		$expected = $this->d->table_quote_start . $table . $this->d->table_quote_end;
		$this->assertEquals($expected, $actual, "Table string name was formatted incorrectly");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFromTable
	*	@depends testGetSelectFromTableName
	*/
	public function testGetSelectFromTable()
	{
		$table = new HelperTable();
		$actual = $this->l->getSelectFromTable($this->d, $table);
		$expected = $this->l->getSelectFromTableName($this->d, $table->getName());
		$this->assertEquals($expected, $actual, "Table object name was formatted incorrectly");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getJoin
	*	@depends testGetFieldFormatted
	*	@depends testGetValue_Column
	*/
	public function testGetJoin_LeftOuter()
	{
		$table_base = 'comments';
		$join_type1 = Join::JOIN_TYPE_LEFT_OUTER;
		$table_join1 = 'user';
		$cond1 = new Condition(
			new Value($table_base . '.user_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join1 . '.id', Value::TYPE_COLUMN)
		);
		$j = new Join($table_base, $join_type1, $table_join1, $cond1);
		$actual = $this->l->getJoin($this->d, $j);
		$expected = new Query();
		$expected->setString(
			$this->d->table_quote_start . $table_base . $this->d->table_quote_end . ' ' .
			SQL::JOIN_TYPE_LEFT_OUTER . ' ' .
			$this->d->table_quote_start . $table_join1 . $this->d->table_quote_end . ' ' .
			SQL::SELECT_JOIN_ON
		);
		$expected->addQuery($this->l->getCondition($this->d, $cond1));
		$this->assertEquals($expected, $actual, "Join query object didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getJoin
	*	@depends testGetFieldFormatted
	*	@depends testGetValue_Column
	*/
	public function testGetJoin_Inner()
	{
		$table_base = 'comments';
		$join_type1 = Join::JOIN_TYPE_INNER;
		$table_join1 = 'user';
		$cond1 = new Condition(
			new Value($table_base . '.user_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join1 . '.id', Value::TYPE_COLUMN)
		);
		$j = new Join($table_base, $join_type1, $table_join1, $cond1);
		$actual = $this->l->getJoin($this->d, $j);
		$expected = new Query();
		$expected->setString(
			$this->d->table_quote_start . $table_base . $this->d->table_quote_end . ' ' .
			SQL::JOIN_TYPE_INNER . ' ' .
			$this->d->table_quote_start . $table_join1 . $this->d->table_quote_end . ' ' .
			SQL::SELECT_JOIN_ON
		);
		$expected->addQuery($this->l->getCondition($this->d, $cond1));
		$this->assertEquals($expected, $actual, "Join query object didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getJoin
	*	@depends testGetFieldFormatted
	*	@depends testGetValue_Column
	*/
	public function testGetJoin_Join()
	{
		$table_base = 'comments';
		$join_type1 = Join::JOIN_TYPE_JOIN;
		$table_join1 = 'user';
		$cond1 = new Condition(
			new Value($table_base . '.user_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join1 . '.id', Value::TYPE_COLUMN)
		);
		$j = new Join($table_base, $join_type1, $table_join1, $cond1);
		$actual = $this->l->getJoin($this->d, $j);
		$expected = new Query();
		$expected->setString(
			$this->d->table_quote_start . $table_base . $this->d->table_quote_end . ' ' .
			SQL::JOIN_TYPE_JOIN . ' ' .
			$this->d->table_quote_start . $table_join1 . $this->d->table_quote_end . ' ' .
			SQL::SELECT_JOIN_ON
		);
		$expected->addQuery($this->l->getCondition($this->d, $cond1));
		$this->assertEquals($expected, $actual, "Join query object didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getJoin
	*	@depends testGetFieldFormatted
	*	@depends testGetValue_Column
	*/
	public function testGetJoin_RightOuter()
	{
		$table_base = 'comments';
		$join_type1 = Join::JOIN_TYPE_RIGHT_OUTER;
		$table_join1 = 'user';
		$cond1 = new Condition(
			new Value($table_base . '.user_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join1 . '.id', Value::TYPE_COLUMN)
		);
		$j = new Join($table_base, $join_type1, $table_join1, $cond1);
		$actual = $this->l->getJoin($this->d, $j);
		$expected = new Query();
		$expected->setString(
			$this->d->table_quote_start . $table_base . $this->d->table_quote_end . ' ' .
			SQL::JOIN_TYPE_RIGHT_OUTER . ' ' .
			$this->d->table_quote_start . $table_join1 . $this->d->table_quote_end . ' ' .
			SQL::SELECT_JOIN_ON
		);
		$expected->addQuery($this->l->getCondition($this->d, $cond1));
		$this->assertEquals($expected, $actual, "Join query object didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getJoin
	*	@depends testGetFieldFormatted
	*	@depends testGetValue_Column
	*/
	public function testGetJoin_Full()
	{
		$table_base = 'comments';
		$join_type1 = Join::JOIN_TYPE_FULL;
		$table_join1 = 'user';
		$cond1 = new Condition(
			new Value($table_base . '.user_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join1 . '.id', Value::TYPE_COLUMN)
		);
		$j = new Join($table_base, $join_type1, $table_join1, $cond1);
		$actual = $this->l->getJoin($this->d, $j);
		$expected = new Query();
		$expected->setString(
			$this->d->table_quote_start . $table_base . $this->d->table_quote_end . ' ' .
			SQL::JOIN_TYPE_FULL . ' ' .
			$this->d->table_quote_start . $table_join1 . $this->d->table_quote_end . ' ' .
			SQL::SELECT_JOIN_ON
		);
		$expected->addQuery($this->l->getCondition($this->d, $cond1));
		$this->assertEquals($expected, $actual, "Join query object didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getJoin
	*	@depends testGetFieldFormatted
	*	@depends testGetValue_Column
	*/
	public function testGetJoin_Multiple()
	{
		$table_base = 'comments';
		$join_type1 = Join::JOIN_TYPE_LEFT_OUTER;
		$table_join1 = 'user';
		$cond1 = new Condition(
			new Value($table_base . '.user_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join1 . '.id', Value::TYPE_COLUMN)
		);
		$join_type2 = Join::JOIN_TYPE_INNER;
		$table_join2 = 'group';
		$cond2 = new Condition(
			new Value($table_join1 . '.group_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join2 . '.id', Value::TYPE_COLUMN)
		);
		$j = new Join($table_base, $join_type1, $table_join1, $cond1);
		$j->addTable($join_type2, $table_join2, $cond2);
		$actual = $this->l->getJoin($this->d, $j);
		$expected = new Query();
		$expected->setString(
			$this->d->table_quote_start . $table_base . $this->d->table_quote_end . ' ' .
			SQL::JOIN_TYPE_LEFT_OUTER . ' ' .
			$this->d->table_quote_start . $table_join1 . $this->d->table_quote_end . ' ' .
			SQL::SELECT_JOIN_ON
		);
		$expected->addQuery($this->l->getCondition($this->d, $cond1));
		$expected->addString(
			SQL::JOIN_TYPE_INNER . ' ' .
			$this->d->table_quote_start . $table_join2 . $this->d->table_quote_end . ' ' .
			SQL::SELECT_JOIN_ON
		);
		$expected->addQuery($this->l->getCondition($this->d, $cond2));
		$this->assertEquals($expected, $actual, "Join query object didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getJoin
	*	@depends testGetFieldFormatted
	*	@depends testGetValue_Column
	*/
	public function testGetJoin_Invalid()
	{
		$e = NULL;
		$table_base = 'comments';
		$join_type1 = Join::JOIN_TYPE_LEFT_OUTER;
		$table_join1 = 'user';
		$cond1 = new Condition(
			new Value($table_base . '.user_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join1 . '.id', Value::TYPE_COLUMN)
		);
		$join_type2 = 'asdfasdfasdf';
		$table_join2 = 'group';
		$cond2 = new Condition(
			new Value($table_join1 . '.group_id', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value($table_join2 . '.id', Value::TYPE_COLUMN)
		);
		$j = new Join($table_base, $join_type1, $table_join1, $cond1);
		$j->addTable($join_type2, $table_join2, $cond2);
		try
		{
			$result = $this->l->getJoin($this->d, $j);
		}
		catch (UnknownTypeException $e)
		{
		}
		$this->assertInstanceOf('\spoof\lib360\db\join\UnknownTypeException', $e, "Failed to throw UnknownException for unknown or invalid join type");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFromView
	*	@depends testGetJoin_Join
	*	@depends testGetJoin_Multiple
	*	@depends testGetJoin_LeftOuter
	*	@depends testGetJoin_RightOuter
	*	@depends testGetJoin_Full
	*	@depends testGetJoin_Inner
	*	@depends testGetSelectFromTable
	*	@depends testGetSelectFromTableName
	*/
	public function testGetSelectFromView()
	{
		$v = new HelperDataView();
		$actual = $this->l->getSelectFromView($this->d, $v);
		$expected = new Query();
		$expected->addQuery($this->l->getJoin($this->d, $v->joins[0]));
		$expected->addString(SQL::SELECT_JOIN_SEPARATOR, FALSE);
		$expected->addString($this->l->getSelectFromTable($this->d, $v->joins[1]));
		$expected->addString(SQL::SELECT_JOIN_SEPARATOR, FALSE);
		$expected->addString($this->l->getSelectFromTableName($this->d, $v->joins[2]));
		$this->assertEquals($expected, $actual, "View query object didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFromView
	*/
	public function testGetSelectFromView_Invalid()
	{
		$e = NULL;
		$v = new HelperDataView();
		$v->joins[] = new \StdClass();
		try
		{
			$this->l->getSelectFromView($this->d, $v);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Failed to throw \InvalidArgumentException on invalid joins element in DBDataView");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFrom
	*	@depends testGetSelectFromTable
	*/
	public function testGetSelectFrom_Table()
	{
		$table = new HelperTable();
		$actual = $this->l->getSelectFrom($this->d, $table);
		$expected = new Query($this->l->getSelectFromTable($this->d, $table));
		$this->assertEquals($expected, $actual, "getSelectFrom with table argument failed to return output of getSelectFromTable");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFrom
	*	@depends testGetSelectFromView
	*/
	public function testGetSelectFrom_View()
	{
		$view = new HelperDataView();
		$actual = $this->l->getSelectFrom($this->d, $view);
		$expected = $this->l->getSelectFromView($this->d, $view);
		$this->assertEquals($expected, $actual, "getSelectFrom with view argument failed to return output of getSelectFromView");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelectFrom
	*/
	public function testGetSelectFrom_Invalid()
	{
		$store = new HelperDataInvalid();
		$e = NULL;
		try
		{
			$this->l->getSelectFrom($this->d, $store);
		}
		catch (\InvalidArgumentException $e)
		{
		}
		$this->assertInstanceOf('\InvalidArgumentException', $e, "Failed to throw InvalidArgumentException when invalid storage type given");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelect
	*	@depends testGetSelectFrom_Table
	*	@depends testGetSelectFields_AllNull
	*/
	public function testGetSelect_TableNoCondition()
	{
		$t = new HelperTable();
		$actual = $this->l->getSelect($this->d, $t);
		$expected = new Query(SQL::SELECT . ' ' . $this->l->getSelectFields($this->d) . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$this->assertEquals($expected, $actual, "Select query object (from table with no condition) didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelect
	*	@depends testGetSelectFrom_Table
	*	@depends testGetSelectFields_AllArray
	*	@depends testGetCondition_Condition
	*/
	public function testGetSelect_TableCondition()
	{
		$t = new HelperTable();
		$c = new Condition(
			new Value('testcolumn', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$actual = $this->l->getSelect($this->d, $t, $c);
		$fields = array();
		$expected = new Query(SQL::SELECT . ' ' . $this->l->getSelectFields($this->d, $fields) . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::WHERE);
		$expected->addQuery($this->l->getCondition($this->d, $c));
		$this->assertEquals($expected, $actual, "Select query object (from table with condition) didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelect
	*	@depends testGetSelectFrom_Table
	*	@depends testGetSelectFields
	*	@depends testGetSelectFields_AllArray
	*	@depends testGetSelectFields_AllNull
	*	@depends testGetCondition_Condition
	*/
	public function testGetSelect_TableConditionFields()
	{
		$t = new HelperTable();
		$c = new Condition(
			new Value('testcolumn', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$fields = array('test1field' => 'test1', 'test2', 'test3field' => 'test3');
		$actual = $this->l->getSelect($this->d, $t, $c, $fields);
		$expected = new Query(SQL::SELECT . ' ' . $this->l->getSelectFields($this->d, $fields) . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::WHERE);
		$expected->addQuery($this->l->getCondition($this->d, $c));
		$this->assertEquals($expected, $actual, "Select query object (from table with condition) didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelect
	*	@depends testGetSelectFrom_View
	*	@depends testGetSelectFields_AllNull
	*/
	public function testGetSelect_ViewNoCondition()
	{
		$t = new HelperDataView();
		$actual = $this->l->getSelect($this->d, $t);
		$expected = new Query(SQL::SELECT . ' ' . $this->l->getSelectFields($this->d) . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$this->assertEquals($expected, $actual, "Select query object (from table with no condition) didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelect
	*	@depends testGetSelectFrom_View
	*	@depends testGetSelectFields_AllArray
	*	@depends testGetCondition_Condition
	*/
	public function testGetSelect_ViewCondition()
	{
		$t = new HelperDataView();
		$c = new Condition(
				new Value('testcolumn', Value::TYPE_COLUMN),
				Condition::OPERATOR_EQUALS,
				new Value(NULL, Value::TYPE_NULL)
		);
		$actual = $this->l->getSelect($this->d, $t, $c);
		$fields = array();
		$expected = new Query(SQL::SELECT . ' ' . $this->l->getSelectFields($this->d, $fields) . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::WHERE);
		$expected->addQuery($this->l->getCondition($this->d, $c));
		$this->assertEquals($expected, $actual, "Select query object (from table with condition) didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getSelect
	*	@depends testGetSelectFrom_View
	*	@depends testGetSelectFields
	*	@depends testGetSelectFields_AllArray
	*	@depends testGetSelectFields_AllNull
	*	@depends testGetCondition_Condition
	*/
	public function testGetSelect_ViewConditionFields()
	{
		$t = new HelperDataView();
		$c = new Condition(
			new Value('testcolumn', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$fields = array('test1field' => 'test1', 'test2', 'test3field' => 'test3');
		$actual = $this->l->getSelect($this->d, $t, $c, $fields);
		$expected = new Query(SQL::SELECT . ' ' . $this->l->getSelectFields($this->d, $fields) . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::WHERE);
		$expected->addQuery($this->l->getCondition($this->d, $c));
		$this->assertEquals($expected, $actual, "Select query object (from table with condition) didn't match the expected result");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getUpdateFields
	*	@depends testGetValue_Column
	*	@depends testGetFieldFormatted
	*/
	public function testGetUpdateFields()
	{
		$field1key = 'testtable1.testfield1';
		$field1value = new Value('testtable2.testfield1', Value::TYPE_COLUMN);
		$field2key = 'testtable1.testfield2';
		$field2value = new Value('testtable2.testfield2', Value::TYPE_COLUMN);
		$fields = array($field1key => $field1value, $field2key => $field2value);
		$actual = $this->l->getUpdateFields($this->d, $fields);
		$expected = new Query($this->l->getFieldFormatted($this->d, $field1key) . ' ' . SQL::EQUALS);
		$expected->addQuery($this->l->getValue($this->d, $field1value));
		$expected->addString(SQL::UPDATE_FIELD_SEPARATOR . ' ' . $this->l->getFieldFormatted($this->d, $field2key) . ' ' . SQL::EQUALS, FALSE);
		$expected->addQuery($this->l->getValue($this->d, $field2value));
		$this->assertEquals($expected, $actual, "Query object didn't match the expected result for update fields");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getUpdate
	*	@depends testGetSelectFrom_Table
	*	@depends testGetUpdateFields
	*/
	public function testGetUpdate_TableNoCondition()
	{
		$t = new HelperTable();
		$field1key = 'field1';
		$field1value = new Value('test1value', Value::TYPE_STRING);
		$field2key = 'field2';
		$field2value = new Value('test2value', Value::TYPE_STRING);
		$fields = array(
			$field1key => $field1value,
			$field2key => $field2value
		);
		$actual = $this->l->getUpdate($this->d, $t, $fields);
		$randKeys = array_keys($actual->values);
		$expected = new Query(SQL::UPDATE);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::UPDATE_SET);
		$expected->addString($this->l->getFieldFormatted($this->d, $field1key) . ' ' . SQL::EQUALS);
		$expected->addString(SQL::BIND_CHAR . $randKeys[0] . SQL::UPDATE_FIELD_SEPARATOR);
		$expected->addString($this->l->getFieldFormatted($this->d, $field2key) . ' ' . SQL::EQUALS);
		$expected->addString(SQL::BIND_CHAR . $randKeys[1]);
		$expected->addValues(array($randKeys[0] => $field1value, $randKeys[1] => $field2value));
		$this->assertEquals($expected, $actual, "Query object didn't match the expected result for update (no condition)");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getUpdate
	*	@depends testGetSelectFrom_Table
	*	@depends testGetUpdateFields
	*/
	public function testGetUpdate_TableCondition()
	{
		$t = new HelperTable();
		$field1key = 'field1';
		$field1value = new Value('test1value', Value::TYPE_STRING);
		$field2key = 'field2';
		$field2value = new Value('test2value', Value::TYPE_STRING);
		$fields = array(
			$field1key => $field1value,
			$field2key => $field2value
		);
		$c = new Condition(
			new Value('field3', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$actual = $this->l->getUpdate($this->d, $t, $fields, $c);
		$randKeys = array_keys($actual->values);
		$expected = new Query(SQL::UPDATE);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::UPDATE_SET);
		$expected->addString($this->l->getFieldFormatted($this->d, $field1key) . ' ' . SQL::EQUALS);
		$expected->addString(SQL::BIND_CHAR . $randKeys[0] . SQL::UPDATE_FIELD_SEPARATOR);
		$expected->addString($this->l->getFieldFormatted($this->d, $field2key) . ' ' . SQL::EQUALS);
		$expected->addString(SQL::BIND_CHAR . $randKeys[1]);
		$expected->addValues(array($randKeys[0] => $field1value, $randKeys[1] => $field2value));
		$expected->addString(SQL::WHERE);
		$expected->addQuery($this->l->getCondition($this->d, $c));
		$this->assertEquals($expected, $actual, "Query object didn't match the expected result for update (no condition)");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getDelete
	*	@depends testGetSelectFrom_Table
	*/
	public function testGetDelete_TableNoCondition()
	{
		$t = new HelperTable();
		$actual = $this->l->getDelete($this->d, $t);
		$expected = new Query(SQL::DELETE . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$this->assertEquals($expected, $actual, "Query object didn't match the expected result for delete (table, no condition)");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getDelete
	*	@depends testGetSelectFrom_Table
	*	@depends testGetCondition_Condition
	*/
	public function testGetDelete_TableCondition()
	{
		$t = new HelperTable();
		$c = new Condition(
			new Value('field3', Value::TYPE_COLUMN),
			Condition::OPERATOR_EQUALS,
			new Value(NULL, Value::TYPE_NULL)
		);
		$actual = $this->l->getDelete($this->d, $t, $c);
		$expected = new Query(SQL::DELETE . ' ' . SQL::FROM);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::WHERE);
		$expected->addQuery($this->l->getCondition($this->d, $c));
		$this->assertEquals($expected, $actual, "Query object didn't match the expected result for delete (table, with condition)");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getInsertFields
	*	@depends testGetFieldFormatted
	*/
	public function testGetInsertFields()
	{
		$field1key = 'table1.field1';
		$field1value = new Value('test1', Value::TYPE_STRING);
		$field2key = 'field2';
		$field2value = new Value('test2', Value::TYPE_STRING);
		$field3key = 'field3';
		$field3value = new Value(NULL, Value::TYPE_NULL);
		$fields = array($field1key => $field1value, $field2key => $field2value, $field3key => $field3value);
		$actual = $this->l->getInsertFields($this->d, $fields);
		$expected = $this->l->getFieldFormatted($this->d, $field1key) . SQL::INSERT_FIELD_SEPARATOR .
					$this->l->getFieldFormatted($this->d, $field2key) . SQL::INSERT_FIELD_SEPARATOR .
					$this->l->getFieldFormatted($this->d, $field3key);
		$this->assertEquals($expected, $actual, "Query string didn't match expected result for insert fields");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getInsertValues
	*	@depends testGetValue_Null
	*/
	public function testGetInsertValues()
	{
		$field1key = 'table1.field1';
		$field1value = new Value(NULL, Value::TYPE_NULL);
		$field2key = 'field2';
		$field2value = new Value(NULL, Value::TYPE_NULL);
		$field3key = 'field3';
		$field3value = new Value(NULL, Value::TYPE_NULL);
		$fields = array($field1key => $field1value, $field2key => $field2value, $field3key => $field3value);
		$actual = $this->l->getInsertValues($this->d, $fields);
		$expected = new Query();
		$expected->addQuery($this->l->getValue($this->d, $field1value));
		$expected->addString(SQL::INSERT_FIELD_SEPARATOR, FALSE);
		$expected->addQuery($this->l->getValue($this->d, $field2value));
		$expected->addString(SQL::INSERT_FIELD_SEPARATOR, FALSE);
		$expected->addQuery($this->l->getValue($this->d, $field3value));
		$this->assertEquals($expected, $actual, "Query string didn't match expected result for insert values");
	}

	/**
	*	@covers \spoof\lib360\db\language\SQL::getInsert
	*	@depends testGetSelectFrom_Table
	*	@depends testGetInsertFields
	*	@depends testGetInsertValues
	*/
	public function testGetInsert()
	{
		$t = new HelperTable();
		$field1key = 'table1.field1';
		$field1value = new Value('test1', Value::TYPE_STRING);
		$field2key = 'field2';
		$field2value = new Value('test2', Value::TYPE_STRING);
		$field3key = 'field3';
		$field3value = new Value(3, Value::TYPE_INTEGER);
		$fields = array($field1key => $field1value, $field2key => $field2value, $field3key => $field3value);
		$actual = $this->l->getInsert($this->d, $t, $fields);
		$randKeys = array_keys($actual->values);
		$expected = new Query(SQL::INSERT . ' ' . SQL::INSERT_INTO);
		$expected->addQuery($this->l->getSelectFrom($this->d, $t));
		$expected->addString(SQL::INSERT_VALUES_WRAPPER_START . ' ' .
			$this->l->getInsertFields($this->d, $fields) . ' ' .
			SQL::INSERT_VALUES_WRAPPER_END . ' ' .
			SQL::INSERT_VALUES . ' ' .
			SQL::INSERT_VALUES_WRAPPER_START);
		$expected->addString(SQL::BIND_CHAR . $randKeys[0] . SQL::INSERT_FIELD_SEPARATOR . ' ' .
			SQL::BIND_CHAR . $randKeys[1] . SQL::INSERT_FIELD_SEPARATOR . ' ' .
			SQL::BIND_CHAR . $randKeys[2]
		);
		$expected->addValues(array($randKeys[0] => $field1value, $randKeys[1] => $field2value, $randKeys[2] => $field3value));
		$expected->addString(SQL::INSERT_VALUES_WRAPPER_END);
		$this->assertEquals($expected, $actual, "Insert query object didn't match the expected result");
	}

}

?>
