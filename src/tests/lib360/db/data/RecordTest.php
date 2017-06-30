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

class RecordTest extends \PHPUnit_Framework_TestCase
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
            $this->getProtectedProperty('\spoof\lib360\db\data\Record', '__type')->getValue($record),
            "Failed to set custom type"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Record::__construct
     */
    protected function getProtectedProperty($class, $property)
    {
        $r = new \ReflectionClass($class);
        $p = $r->getProperty($property);
        $p->setAccessible(true);
        return $p;
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
