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

use spoof\lib360\db\condition\Condition;
use spoof\lib360\db\data\ModelException;
use spoof\lib360\db\data\RecordNotFoundException;
use spoof\lib360\db\value\Value;

class ModelTest extends \spoof\tests\lib360\db\DatabaseTestCase
{

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     * @covers \spoof\lib360\db\data\Model::get
     * @covers \spoof\lib360\db\data\Model::getByKey
     * @covers \spoof\lib360\db\data\Model::setRecord
     */
    public function testGetByKey_Success()
    {
        $user = HelperModelUser::getByKey(1);
        $this->assertEquals(
            array('1', 'Juno', 'Jiana'),
            array($user->get('id'), $user->get('name_first'), $user->get('name_last'))
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     */
    public function testGetByKey_FailNoTable()
    {
        $e = null;
        try {
            HelperModelUserNoTable::getByKey(1);
        } catch (ModelException $e) {
        }
        $this->assertInstanceOf('\spoof\lib360\db\data\ModelException', $e);
    }

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     */
    public function testGetByKey_FailNoTableKey()
    {
        $e = null;
        try {
            HelperModelUserNoKey::getByKey(1);
        } catch (ModelException $e) {
        }
        $this->assertInstanceOf('\spoof\lib360\db\data\ModelException', $e);
    }

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     */
    public function testGetByKey_FailNoTableFields()
    {
        $e = null;
        try {
            HelperModelUserNoFields::getByKey(1);
        } catch (ModelException $e) {
        }
        $this->assertInstanceOf('\spoof\lib360\db\data\ModelException', $e);
    }

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     * @covers \spoof\lib360\db\data\Model::getByKey
     */
    public function testGetByKey_NotFound()
    {
        $e = null;
        try {
            HelperModelUser::getByKey(999999); // doesn't exist
        } catch (RecordNotFoundException $e) {
        }
        $this->assertInstanceOf('\spoof\lib360\db\data\RecordNotFoundException', $e);
    }
    
    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     * @covers \spoof\lib360\db\data\Model::create
     */
    public function testCreate()
    {
        $user = HelperModelUser::create();
        $this->assertEquals(
            array(null, null, null, null, null),
            array(
                $user->get('id'),
                $user->get('date_created'),
                $user->get('name_first'),
                $user->get('name_last'),
                $user->get('status'),
            ),
            "Initial model values didn't match expected"
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     * @covers \spoof\lib360\db\data\Model::createFromRecord
     */
    public function testCreateFromRecord()
    {
        $userId = 1;
        $userTable = new HelperTableUser();
        $userRecord = $userTable->selectRecord($userId);
        $userModel = HelperModelUser::createFromRecord($userRecord);
        $this->assertEquals(
            array($userId, 'Juno', 'Jiana'),
            array($userModel->get('id'), $userModel->get('name_first'), $userModel->get('name_last'))
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     * @covers \spoof\lib360\db\data\Model::getByAttributes
     */
    public function testGetByAttributes()
    {
        $users = HelperModelUser::getByAttributes(array('status' => 'active'));
        $this->assertEquals(2, count($users));
        foreach ($users as $user) {
            $this->assertEquals('active', $user->get('status'));
        }
        $this->assertEquals(
            array(
                0 => array('id' => 2, 'name_first' => 'Numa', 'name_last' => null, 'status' => 'active'),
                1 => array('id' => 4, 'name_first' => 'Mono', 'name_last' => 'Sailor', 'status' =>'active'),
            ),
            array(
                0 => array(
                    'id' => $users[0]->get('id'),
                    'name_first' => $users[0]->get('name_first'),
                    'name_last' => $users[0]->get('name_last'),
                    'status' => $users[0]->get('status'),
                ),
                1 => array(
                    'id' => $users[1]->get('id'),
                    'name_first' => $users[1]->get('name_first'),
                    'name_last' => $users[1]->get('name_last'),
                    'status' => $users[1]->get('status'),
                ),
            )
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Model::__construct
     * @covers \spoof\lib360\db\data\Model::getByCondition
     */
    public function testGetByCondition()
    {
        $condition = new Condition(
            new Value('status', Value::TYPE_COLUMN),
            Condition::OPERATOR_EQUALS,
            new Value('active', Value::TYPE_STRING)
        );
        $users = HelperModelUser::getByCondition($condition);
        $this->assertEquals(2, count($users));
        foreach ($users as $user) {
            $this->assertEquals('active', $user->get('status'));
        }
        $this->assertEquals(
            array(
                0 => array('id' => 2, 'name_first' => 'Numa', 'name_last' => null, 'status' => 'active'),
                1 => array('id' => 4, 'name_first' => 'Mono', 'name_last' => 'Sailor', 'status' =>'active'),
            ),
            array(
                0 => array(
                    'id' => $users[0]->get('id'),
                    'name_first' => $users[0]->get('name_first'),
                    'name_last' => $users[0]->get('name_last'),
                    'status' => $users[0]->get('status'),
                ),
                1 => array(
                    'id' => $users[1]->get('id'),
                    'name_first' => $users[1]->get('name_first'),
                    'name_last' => $users[1]->get('name_last'),
                    'status' => $users[1]->get('status'),
                ),
            )
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Model::set
     * @depends testGetByKey_Success
     */
    public function testSet_Success()
    {
        $firstNameUpdated = 'first name updated';
        $user = HelperModelUser::getByKey(1);
        $user->set('name_first', $firstNameUpdated);
        $this->assertEquals($firstNameUpdated, $user->get('name_first'));
    }

    /**
     * @covers \spoof\lib360\db\data\Model::set
     * @depends testGetByKey_Success
     */
    public function testSet_Fail()
    {
        $user = HelperModelUser::getByKey(1);
        $e = null;
        try{
            $user->set('invalid_field', 'value');
        } catch (\OutOfBoundsException $e) {
        }
        $this->assertInstanceOf('\OutOfBoundsException', $e);
    }

    /**
     * @covers \spoof\lib360\db\data\Model::store
     * @covers \spoof\lib360\db\data\Model::hasKey
     * @depends testGetByKey_Success
     */
    public function testStore_Update()
    {
        $userId = 1;
        $lastNameUpdated = 'last name updated';
        $userModel = HelperModelUser::getByKey($userId);
        $userModel->set('name_last', $lastNameUpdated);
        $userModel->store();
        $userModelUpdated = HelperModelUser::getByKey($userId);
        $this->assertEquals($lastNameUpdated, $userModelUpdated->get('name_last'), "Updated value didn't match");
    }

    /**
     * @covers \spoof\lib360\db\data\Model::store
     * @covers \spoof\lib360\db\data\Model::hasKey
     * @depends testGetByKey_Success
     */
    public function testStore_Insert()
    {
        $lastName = 'hello from testStore_Insert';
        $status = 'active';
        $userModel = HelperModelUser::create();
        $userModel->set('name_last', $lastName);
        $userModel->set('status', $status);
        $userModel->store();
        $userModelUpdated = HelperModelUser::getByKey($userModel->get('id'));
        $this->assertEquals($lastName, $userModelUpdated->get('name_last'), "Insert value didn't match");
    }

    /**
     * @covers \spoof\lib360\db\data\Model::delete
     * @depends testGetByKey_Success
     * @depends testGetByKey_NotFound
     */
    public function testDelete()
    {
        $userId = 4;
        $userModel = HelperModelUser::getByKey($userId);
        $e = null;
        $userModelDeletedResult = $userModel->delete();
        try {
            HelperModelUser::getByKey($userId);
        } catch (RecordNotFoundException $e) {
        }
        $this->assertInstanceOf('\spoof\lib360\db\data\RecordNotFoundException', $e);
        $this->assertEquals(
            array(true, null, null, null, null, null),
            array(
                $userModelDeletedResult,
                $userModel->get('id'),
                $userModel->get('date_created'),
                $userModel->get('name_first'),
                $userModel->get('name_last'),
                $userModel->get('status'),
            )
        );
    }

    /**
     * @covers \spoof\lib360\db\data\Model::toArray
     * @depends testSet_Success
     */
    public function testToArray()
    {
        $model = HelperModelUser::create();
        $model->set('name_first', 'value 1');
        $model->set('name_last', 'value 2');
        $model->set('status', 'value 3');
        $expected = array(
            'id' => null,
            'date_created' => null,
            'name_first' => 'value 1',
            'name_last' => 'value 2',
            'status' => 'value 3',
        );
        $this->assertEquals($expected, $model->toArray(), "Array conversion didn't match expected");
    }

}

?>
