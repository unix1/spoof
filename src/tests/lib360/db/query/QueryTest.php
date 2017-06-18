<?php

namespace spoof\tests\lib360\db\query;

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

use spoof\lib360\db\query\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \spoof\lib360\db\query\Query::__construct
     */
    public function testConstruct_Default()
    {
        $query = new Query();
        $actual = array($query->query, $query->values);
        $expected = array('', array());
        $this->assertEquals($expected, $actual, "Failed to initialize with default values");
    }

    /**
     * @covers \spoof\lib360\db\query\Query::__construct
     */
    public function testConstruct_Custom()
    {
        $queryString = 'test string';
        $queryValues = array('key1' => 'value1', 'key2' => 'value2');
        $query = new Query($queryString, $queryValues);
        $actual = array($query->query, $query->values);
        $expected = array($queryString, $queryValues);
        $this->assertEquals($expected, $actual, "Failed to initialize with custom supplied values");
    }

    /**
     * @covers \spoof\lib360\db\query\Query::setString
     */
    public function testSetString_New()
    {
        $query_string = 'test string';
        $query = new Query();
        $query->setString($query_string);
        $this->assertEquals(
            $query_string,
            $query->query,
            "The string property of the object didn't match the string that was initially set"
        );
    }

    /**
     * @covers  \spoof\lib360\db\query\Query::setString
     * @depends testSetString_New
     */
    public function testSetString_Override()
    {
        $query_string = 'test string override';
        $query = new Query();
        $query->setString('test string 1');
        $query->setString($query_string);
        $this->assertEquals(
            $query_string,
            $query->query,
            "The string property of the object didn't match the string that was set to override existing value"
        );
    }

    /**
     * @covers  \spoof\lib360\db\query\Query::addString
     * @depends testSetString_New
     */
    public function testAddString_InitialHintTrue()
    {
        $query_string = 'test string';
        $query = new Query();
        $query->addString($query_string, true);
        $this->assertEquals($query_string, $query->query, "Failed to add initial string with space hint");
    }

    /**
     * @covers  \spoof\lib360\db\query\Query::addString
     * @depends testSetString_New
     */
    public function testAddString_InitialHintFalse()
    {
        $query_string = 'test string';
        $query = new Query();
        $query->addString($query_string, false);
        $this->assertEquals($query_string, $query->query, "Failed to add initial string with no space hint");
    }

    /**
     * @covers  \spoof\lib360\db\query\Query::addString
     * @depends testSetString_New
     */
    public function testAddString_AdditionalHintTrue()
    {
        $query_string1 = 'test string';
        $query_string2 = 'test additional string';
        $query = new Query();
        $query->setString($query_string1);
        $query->addString($query_string2, true);
        $this->assertEquals(
            $query_string1 . ' ' . $query_string2,
            $query->query,
            "Failed to add additional string with space hint"
        );
    }

    /**
     * @covers  \spoof\lib360\db\query\Query::addString
     * @depends testSetString_New
     */
    public function testAddString_AdditionalHintFalse()
    {
        $query_string1 = 'test string';
        $query_string2 = 'test additional string';
        $query = new Query();
        $query->setString($query_string1);
        $query->addString($query_string2, false);
        $this->assertEquals(
            $query_string1 . $query_string2,
            $query->query,
            "Failed to add additional string with no space hint"
        );
    }

    /**
     * @covers \spoof\lib360\db\query\Query::addValues
     */
    public function testAddValues_Initial()
    {
        $query = new Query();
        $expected = array('key1' => 'value1', 'key2' => 'value2');
        $query->addValues($expected);
        $this->assertEquals($expected, $query->values, "Failed to add initial values");
    }

    /**
     * @covers  \spoof\lib360\db\query\Query::addValues
     * @depends testAddValues_Initial
     */
    public function testAddValues_Additional()
    {
        $query = new Query();
        $arr1 = array('key1' => 'value1', 'key2' => 'value2');
        $arr2 = array('key3' => 'value3', 'key4' => 'value4');
        $query->addValues($arr1);
        $query->addValues($arr2);
        $expected = array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3', 'key4' => 'value4');
        $this->assertEquals($expected, $query->values, "Failed to add additional values");
    }

    /**
     * @covers \spoof\lib360\db\query\Query::addQuery
     */
    public function testAddQuery()
    {
        $query_initial_values = array('one' => 'value', 'two' => 'another value');
        $query_initial_string = 'Initial query string';
        $query_initial = new Query($query_initial_string, $query_initial_values);
        $query_additional_values = array('key1' => 'value1', 'key2' => 'value2');
        $query_additional_string = 'Test query string';
        $query_additional = new Query($query_additional_string, $query_additional_values);
        $query_initial->addQuery($query_additional);
        $expected = array(
            array('one' => 'value', 'two' => 'another value', 'key1' => 'value1', 'key2' => 'value2'),
            $query_initial_string . ' ' . $query_additional_string
        );
        $this->assertEquals(
            $expected,
            array($query_initial->values, $query_initial->query),
            "Failed to add query object"
        );
    }

}

?>
