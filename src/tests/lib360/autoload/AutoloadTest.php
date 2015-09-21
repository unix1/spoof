<?php

namespace spoof\tests\lib360\autoload;

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

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'lib360' . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . 'Autoload.php');
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'lib360' . DIRECTORY_SEPARATOR . 'Lib360.php');

use \spoof\lib360\autoload\Autoload;
use \spoof\lib360\Lib360;

class AutoloadTest extends \PHPUnit_Framework_TestCase
{

	protected $initialSPLExtensions;

	protected function setUp()
	{
		Lib360::reset();
		$this->initialSPLExtensions = spl_autoload_extensions();
		spl_autoload_extensions('.php');
	}

	protected function tearDown()
	{
		spl_autoload_extensions($this->initialSPLExtensions);
		Lib360::reset();
		Lib360::initialize();
	}

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers \spoof\lib360\autoload\Autoload::import
	*/
	public function testImport()
	{
		$directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		Autoload::import($directory);
		$dirsProperty = $this->getProtectedProperty('\spoof\lib360\autoload\Autoload', 'dirs');
		$this->assertArrayHasKey($directory, $dirsProperty->getValue(), "import method didn't register given directory");
	}

	/**
	*	@covers \spoof\lib360\autoload\Autoload::remove
	*	@depends testImport
	*/
	public function testRemove()
	{
		$directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		Autoload::remove($directory);
		$dirsProperty = $this->getProtectedProperty('\spoof\lib360\autoload\Autoload', 'dirs');
		$this->assertArrayNotHasKey($directory, $dirsProperty->getValue(), "remove method didn't unregister given directory");
	}

	/**
	*	@covers \spoof\lib360\autoload\Autoload::load
	*/
	public function testLoad_Success()
	{
		$class = 'spoof\tests\lib360\autoload\AutoloadTestHelper';
		//Autoload::import(dirname(__FILE__) . DIRECTORY_SEPARATOR);
		Autoload::load($class);
		$this->assertTrue(class_exists($class, FALSE), "load method didn't load class $class");
	}

	/**
	*	@covers \spoof\lib360\autoload\Autoload::load
	*	@depends testLoad_Success
	*/
	public function testLoad_SuccessSPL()
	{
		$class = '\spoof\tests\lib360\autoload\AutoloadTestHelper';
		$o = new $class();
		spl_autoload_register('\spoof\lib360\autoload\Autoload::load');
		$this->assertInstanceOf($class, $o, "SPL autoload didn't load class $class");
		spl_autoload_unregister('\spoof\lib360\autoload\Autoload::load');
	}

	/**
	*	@covers \spoof\lib360\autoload\Autoload::load
	*	@depends testRemove
	*/
	public function testLoad_FailSPL()
	{
		$class = '\spoof\tests\lib360\autoload\AutoloadTestHelperNonexistentClass';
		spl_autoload_register('\spoof\lib360\autoload\Autoload::load');
		$this->assertFalse(class_exists($class, TRUE), "SPL autoload shouldn't have succeeded in loading class $class because this class shouldn't exist");
		spl_autoload_unregister('\spoof\lib360\autoload\Autoload::load');
	}

}

?>
