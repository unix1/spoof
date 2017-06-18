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

namespace spoof\tests\lib360\db\connection;

use spoof\lib360\db\connection\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \spoof\lib360\db\connection\Config::__construct
     */
    public function testConstruct_RequiredArgs()
    {
        $dsn = 'mysql:localhost';
        $config = new Config($dsn);
        $this->assertEquals($dsn, $config->dsn, "Failed to set dsn during instantiation");
    }

    /**
     * @covers \spoof\lib360\db\connection\Config::__construct
     */
    public function testConstruct_AllArgs()
    {
        $dsn = 'mysql:localhost';
        $user = 'test';
        $pass = 'pass';
        $driver = 'test_driver';
        $options = array('opt1' => 'option1', 'opt2' => 'option2');
        $config = new Config($dsn, $user, $pass, $options, $driver);
        $this->assertEquals(
            array($dsn, $user, $pass, $options, $driver),
            array($config->dsn, $config->username, $config->password, $config->options, $config->driver),
            "Failed to set arguments during instantiation"
        );
    }

}

?>
