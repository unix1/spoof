<?php

namespace lib360\tests\autoload;

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

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . 'Autoload.php');
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Lib360.php');

class AutoloadTest extends \PHPUnit_Framework_TestCase
{

	protected $initialSPLExtensions;

	protected function setUp()
	{
		\lib360\Lib360::reset();
		$this->initialSPLExtensions = spl_autoload_extensions();
		spl_autoload_extensions('.php');
	}

	protected function tearDown()
	{
		spl_autoload_extensions($this->initialSPLExtensions);
		\lib360\Lib360::reset();
		\lib360\Lib360::initialize();
	}

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers \lib360\autoload\Autoload::import
	*/
	public function testImport()
	{
		$directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		\lib360\autoload\Autoload::import($directory);
		$dirsProperty = $this->getProtectedProperty('\lib360\autoload\Autoload', 'dirs');
		$this->assertArrayHasKey($directory, $dirsProperty->getValue(), "import method didn't register given directory");
	}

	/**
	*	@covers \lib360\autoload\Autoload::remove
	*	@depends testImport
	*/
	public function testRemove()
	{
		$directory = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		\lib360\autoload\Autoload::remove($directory);
		$dirsProperty = $this->getProtectedProperty('\lib360\autoload\Autoload', 'dirs');
		$this->assertArrayNotHasKey($directory, $dirsProperty->getValue(), "remove method didn't unregister given directory");
	}

	/**
	*	@covers \lib360\autoload\Autoload::load
	*	@depends testRemove
	*/
	public function testLoad_Success()
	{
		$class = 'lib360\tests\autoload\AutoloadTestHelper';
		//\lib360\autoload\Autoload::import(dirname(__FILE__) . DIRECTORY_SEPARATOR);
		\lib360\autoload\Autoload::load($class);
		$this->assertTrue(class_exists($class, FALSE), "load method didn't load class $class");
	}

	/**
	*	@covers \lib360\autoload\Autoload::load
	*	@depends testLoad_Success
	*/
	public function testLoad_SuccessSPL()
	{
		$class = '\lib360\tests\autoload\AutoloadTestHelper';
		$o = new $class();
		spl_autoload_register('\lib360\autoload\Autoload::load');
		$this->assertInstanceOf($class, $o, "SPL autoload didn't load class $class");
		spl_autoload_unregister('\lib360\autoload\Autoload::load');
	}

	/**
	*	@covers \lib360\autoload\Autoload::load
	*	@depends testRemove
	*/
	public function testLoad_FailSPL()
	{
		$class = '\lib360\tests\autoload\AutoloadTestHelperNonexistentClass';
		spl_autoload_register('\lib360\autoload\Autoload::load');
		$this->assertFalse(class_exists($class, TRUE), "SPL autoload shouldn't have succeeded in loading class $class because this class shouldn't exist");
		spl_autoload_unregister('\lib360\autoload\Autoload::load');
	}

}

?>