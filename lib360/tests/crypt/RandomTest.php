<?php

namespace lib360\tests\crypt;

/*
    This is Spoof.
    Copyright (C) 2011-2013  Spoof project.

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

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'initialize.php');

class RandomTest extends \PHPUnit_Framework_TestCase
{

	/**
	*	@covers \lib360\crypt\Random::getRandomTag
	*/
	public function testGetString()
	{
		$tries = 1000;
		$result = array();
		for ($i = 0; $i < $tries; ++$i)
		{
			$key = \lib360\crypt\Random::getString(4, TRUE, TRUE);
			$result[$key] = 1;
		}
		$actual = count($result);
		$this->assertEquals($tries, $actual, "Expected $tries result, but got $actual");
	}

}

?>