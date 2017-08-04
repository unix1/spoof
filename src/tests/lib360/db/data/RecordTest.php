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

use spoof\lib360\db\data\Record;
use spoof\tests\TestCase;

class RecordTest extends TestCase
{

    /**
     * @covers \spoof\lib360\db\data\Record::__construct
     */
    public function testConstruct_Default()
    {
        $record = new Record();
        $this->assertInstanceOf('\spoof\lib360\db\data\Record', $record, "Failed to create an instance");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::__construct
     */
    public function testConstruct_CustomType()
    {
        $type = 'test';
        $record = new Record($type);
        $this->assertEquals(
            $type,
            $this->getProtectedProperty($record, '__type'),
            "Failed to set custom type"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Record::__set
     */
    public function test__Set()
    {
        $testValue = 'test1';
        $record = new Record();
        $record->test1 = $testValue;
        $this->assertEquals($testValue, $record->offsetGet('test1'), "Failed to set value");
    }

    /**
     * @covers  \spoof\lib360\db\data\Record::__get
     * @depends test__Set
     */
    public function test__Get_Fail()
    {
        $record = new Record();
        $e = null;
        try {
            $v = $record->test;
        } catch (\OutOfBoundsException $e) {
        }
        $this->assertInstanceOf('\OutOfBoundsException', $e, "Failed to throw exception when offset doesn't exist");
    }

    /**
     * @covers  \spoof\lib360\db\data\Record::__get
     * @depends test__Set
     */
    public function test__Get_Success()
    {
        $record = new Record();
        $e = null;
        $v = null;
        $testValue = 'test value';
        $record->test = $testValue;
        try {
            $v = $record->test;
        } catch (\OutOfBoundsException $e) {
        }
        $this->assertEquals($testValue, $v, "Retrieved value doesn't match the value set");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::set
     * @depends test__Set
     */
    public function testSet_Success()
    {
        $testValue = 'test value';
        $record = new Record();
        $record->test1 = 'original value';
        $record->set('test1', $testValue);
        $this->assertEquals($testValue, $record->offsetGet('test1'), "Failed to set value");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::set
     */
    public function testSet_Fail()
    {
        $testValue = 'test value';
        $record = new Record();
        try {
            $record->set('test', $testValue);
        } catch (\OutOfBoundsException $e) {
        }
        $this->assertInstanceOf('\OutOfBoundsException', $e, "Failed to throw exception when offset doesn't exist");
    }
    /**
     * @covers  \spoof\lib360\db\data\Record::get
     * @depends testSet_Success
     */
    public function testGet_Fail()
    {
        $record = new Record();
        $e = null;
        try {
            $v = $record->get('test');
        } catch (\OutOfBoundsException $e) {
        }
        $this->assertInstanceOf('\OutOfBoundsException', $e, "Failed to throw exception when offset doesn't exist");
    }

    /**
     * @covers  \spoof\lib360\db\data\Record::get
     * @depends test__Set
     */
    public function testGet_Success()
    {
        $record = new Record();
        $e = null;
        $v = null;
        $testValue = 'test value';
        $record->test = $testValue;
        try {
            $v = $record->get('test');
        } catch (\OutOfBoundsException $e) {
        }
        $this->assertEquals($testValue, $v, "Retrieved value doesn't match the value set");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::getModified
     * @depends testSet_Success
     */
    public function testGetModified()
    {
        $value1 = 'value 1';
        $value3 = 'value 3';
        $record = new Record();
        $record->key1 = 'value 1 original';
        $record->key2 = 'value 2 original';
        $record->key3 = 'value 3 original';
        $record->set('key1', $value1);
        $record->set('key3', $value3);
        $expected = array('key1' => $value1, 'key3' => $value3);
        $actual = $record->getModified();
        $this->assertEquals($expected, $actual, "Modified record array is incorrect");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::getOriginal
     * @depends testSet_Success
     */
    public function testGetOriginal()
    {
        $value1 = 'value 1 original';
        $value2 = 'value 2 original';
        $record = new Record();
        $record->key1 = $value1;
        $record->key2 = $value2;
        $record->set('key1', 'value 1 updated');
        $expected = array('key1' => $value1, 'key2' => $value2);
        $actual = array('key1' => $record->getOriginal('key1'), 'key2' => $record->getOriginal('key2'));
        $this->assertEquals($expected, $actual, "Original value incorrect after update");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::isModified
     * @depends testSet_Success
     */
    public function testIsModified_False()
    {
        $record = new Record();
        $record->key1 = 'value 1';
        $record->key2 = 'value 2';
        $record->key1 = 'value 1 updated';
        $this->assertFalse($record->isModified(), "Modified flag was not false");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::isModified
     * @depends testSet_Success
     */
    public function testIsModified_True()
    {
        $record = new Record();
        $record->key1 = 'value 1';
        $record->set('key1', 'value 1 updated');
        $this->assertTrue($record->isModified(), "Modified flag was not true");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::clearModified()
     * @depends testIsModified_True
     */
    public function testClearModified()
    {
        $record = new Record();
        $record->key1 = 'value 1';
        $record->set('key1', 'value 1 updated');
        $record->clearModified();
        $expected = array(false, array());
        $actual = array($record->isModified(), $record->getModified());
        $this->assertEquals($expected, $actual, "Modified flag and/or array didn't reset");
    }

    /**
     * @covers \spoof\lib360\db\data\Record::clear
     * @depends testClearModified
     */
    public function testClear()
    {
        $record = new Record();
        $record->key1 = 'value 1';
        $record->key2 = 'value 2';
        $record->set('key1', 'value 1 updated');
        $record->clear();
        $this->assertEquals(
            array(false, array(), null, null),
            array(
                $record->isModified(),
                $record->getModified(),
                $record->get('key1'),
                $record->get('key2'),
            )
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Record::toArray
     * @depends test__Set
     * @depends test__Get_Success
     */
    public function testToArray()
    {
        $record = new Record();
        $record->key1 = 'value 1';
        $record->key2 = 'value 2';
        $expected = array('key1' => 'value 1', 'key2' => 'value 2');
        $this->assertEquals($expected, $record->toArray(), "Array conversion didn't match expected");
    }

    /**
     * @covers  \spoof\lib360\db\data\Record::toXML
     * @depends test__Set
     * @depends test__Get_Success
     */
    public function testToXML()
    {
        $record = new Record('test');
        $expectedXMLString = '<record type="test"><test1>test 1 value</test1><test2>test 2 value</test2></record>';
        $expectedXML = new \DOMDocument();
        $expectedXML->loadXML($expectedXMLString);
        $record->test1 = 'test 1 value';
        $record->test2 = 'test 2 value';
        $resultXML = $record->toXML();
        $this->assertEqualXMLStructure(
            $expectedXML->firstChild,
            $resultXML->firstChild,
            true,
            "Failed to return correct XML structure"
        );
    }

}

?>
