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
use spoof\lib360\db\data\RecordList;

class RecordListTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \spoof\lib360\db\data\RecordList::__construct
     */
    public function testConstruct_Default()
    {
        $l = new RecordList();
        $this->assertInstanceOf('\spoof\lib360\db\data\RecordList', $l, "Failed to create an instance");
    }

    /**
     * @covers \spoof\lib360\db\data\RecordList::__construct
     */
    public function testConstruct_Record()
    {
        $r0 = new Record();
        $r0->test0 = 'test 0 value';
        $l = new RecordList(array(0 => $r0));
        $this->assertEquals($r0, $l->offsetGet(0), "Failed to set record element in constructor");
    }

    /**
     * @covers \spoof\lib360\db\data\RecordList::__construct
     */
    public function testConstruct_Records()
    {
        $r0 = new Record();
        $r0->test0 = 'test 0 value';
        $r1 = new Record();
        $r1->test1 = 'test 1 value';
        $l = new RecordList(array(0 => $r0, 1 => $r1));
        $this->assertEquals($r1, $l->offsetGet(1), "Failed to set record element in constructor");
    }

    /**
     * @covers \spoof\lib360\db\data\RecordList::toXML
     */
    public function testToXML()
    {
        $r = new Record('test');
        $r->test1 = 'test 1 value';
        $r->test2 = 'test 2 value';
        $l = new RecordList(array($r));
        $expectedXMLString = '<recordlist type="RecordList"><record type="test"><test1>test 1 value</test1><test2>test 2 value</test2></record></recordlist>';
        $expectedXML = new \DomDocument();
        $expectedXML->loadXML($expectedXMLString);
        $resultXML = $l->toXML();
        $this->assertEqualXMLStructure(
            $expectedXML->firstChild,
            $resultXML->firstChild,
            true,
            "Failed to return correct XML structure"
        );
    }

}

?>
