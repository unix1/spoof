<?php

namespace spoof\tests\lib360\db\connection;

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

use \spoof\lib360\db\connection\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
	/**
	*	@covers \spoof\lib360\db\connection\Config::__construct
	*/
	public function testConstruct_RequiredArgs()
	{
		$dsn = 'mysql:localhost';
		$o = new Config($dsn);
		$this->assertEquals($dsn, $o->dsn, "Failed to set dsn during instantiation");
	}

	/**
	*	@covers \spoof\lib360\db\connection\Config::__construct
	*/
	public function testConstruct_AllArgs()
	{
		$dsn = 'mysql:localhost';
		$user = 'test';
		$pass = 'pass';
		$driver = 'test_driver';
		$options = array('opt1' => 'option1', 'opt2' => 'option2');
		$o = new Config($dsn, $user, $pass, $options, $driver);
		$this->assertEquals(array($dsn, $user, $pass, $options, $driver), array($o->dsn, $o->username, $o->password, $o->options, $o->driver), "Failed to set arguments during instantiation");
	}

}

?>
