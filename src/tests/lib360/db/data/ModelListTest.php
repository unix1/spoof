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

use spoof\lib360\db\data\ModelList;
use spoof\lib360\db\data\RecordList;

class ModelListTest extends \spoof\tests\lib360\db\DatabaseTestCase
{

    /**
     * @covers \spoof\lib360\db\data\ModelList::__construct
     */
    public function testConstruct()
    {
        $modelList = new ModelList(new RecordList(), '\spoof\tests\lib360\db\data\HelperModelUser');
        $this->assertInstanceOf('\spoof\lib360\db\data\ModelList', $modelList);
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::count
     * @depends testConstruct
     */
    public function testCount()
    {
        $usersId0 = HelperModelUser::getByAttributes(array('id' => 0));
        $usersId1 = HelperModelUser::getByAttributes(array('id' => 1));
        $usersActive = HelperModelUser::getByAttributes(array('status' => 'active'));
        $this->assertEquals(
            array(0, 1, 2),
            array(
                count($usersId0),
                count($usersId1),
                count($usersActive),
            ),
            "Expected model data counts dind't match"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::current
     * @covers \spoof\lib360\db\data\ModelList::rewind
     * @covers \spoof\lib360\db\data\ModelList::valid
     * @depends testConstruct
     */
    public function testCurrent()
    {
        $usersId1 = HelperModelUser::getByAttributes(array('id' => 1));
        foreach ($usersId1 as $user1) {
            break;
        }
        $this->assertEquals(
            array(1, 'Juno', 'Jiana', 'inactive'),
            array(
                $user1->get('id'),
                $user1->get('name_first'),
                $user1->get('name_last'),
                $user1->get('status'),
            ),
            "Current record from record list didn't match expected data"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::key
     * @covers \spoof\lib360\db\data\ModelList::rewind
     * @covers \spoof\lib360\db\data\ModelList::valid
     * @depends testConstruct
     */
    public function testKey()
    {
        $users = HelperModelUser::getByAttributes(array('status' => 'active'));
        $keys = array();
        foreach ($users as $key => $user) {
            $keys[] = $key;
        }
        $this->assertEquals(array(0, 1), $keys, "Keys didn't match expected values");
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::next
     * @covers \spoof\lib360\db\data\ModelList::rewind
     * @covers \spoof\lib360\db\data\ModelList::valid
     * @depends testConstruct
     */
    public function testNext()
    {
        $users = HelperModelUser::getByAttributes(array('status' => 'active'));
        foreach ($users as $user) {
        }
        $this->assertEquals(
            array(4, 'Mono', 'Sailor', 'active'),
            array(
                $user->get('id'),
                $user->get('name_first'),
                $user->get('name_last'),
                $user->get('status'),
            ),
            "Last record from record list didn't match expected data"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::offsetExists
     * @depends testConstruct
     */
    public function testOffsetExists()
    {
        $users = HelperModelUser::getByAttributes(array('status' => 'active'));
        $this->assertEquals(
            array(true, true, false),
            array(
                isset($users[0]),
                isset($users[1]),
                isset($users[2]),
            )
        );
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::offsetGet
     * @depends testConstruct
     */
    public function testOffsetGet()
    {
        $users = HelperModelUser::getByAttributes(array('status' => 'inactive'));
        $this->assertEquals(
            array(3, 'Steep', 'Pinata', 'inactive'),
            array(
                $users[1]->get('id'),
                $users[1]->get('name_first'),
                $users[1]->get('name_last'),
                $users[1]->get('status'),
            ),
            "Accessing record by offset didn't match expected data"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::offsetSet
     * @depends testConstruct
     * @depends testOffsetExists
     * @depends testOffsetGet
     */
    public function testOffsetSet()
    {
        $users = HelperModelUser::getByAttributes(array('status' => 'inactive'));
        $users[1] = '123';
        $this->assertEquals(
            array('123', 1, 'Juno', 'Jiana', 'inactive'),
            array(
                $users[1],
                $users[0]->get('id'),
                $users[0]->get('name_first'),
                $users[0]->get('name_last'),
                $users[0]->get('status'),
            ),
            "Data didn't match after offsetSet"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\ModelList::offsetUnset
     * @depends testConstruct
     * @depends testOffsetExists
     * @depends testOffsetGet
     */
    public function testOffsetUnset()
    {
        $users = HelperModelUser::getByAttributes(array('status' => 'inactive'));
        unset($users[1]);
        $this->assertEquals(
            array(false, 1, 'Juno', 'Jiana', 'inactive'),
            array(
                isset($users[1]),
                $users[0]->get('id'),
                $users[0]->get('name_first'),
                $users[0]->get('name_last'),
                $users[0]->get('status'),
            ),
            "Data didn't match after offsetUnset"
        );
    }

}

?>
