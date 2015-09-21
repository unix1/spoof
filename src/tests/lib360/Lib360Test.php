<?php

namespace spoof\tests\lib360;

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

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'lib360' . DIRECTORY_SEPARATOR . 'Lib360.php');

class Lib360Test extends \PHPUnit_Framework_TestCase
{

	protected function getProtectedProperty($class, $property)
	{
		$r = new \ReflectionClass($class);
		$p = $r->getProperty($property);
		$p->setAccessible(true);
		return $p;
	}

	/**
	*	@covers \spoof\lib360\Lib360::initialize
	*/
	public function testInitialize_AutoloadClass()
	{
		\spoof\lib360\Lib360::reset();
		\spoof\lib360\Lib360::initialize();
		$autoloadClassName = \spoof\lib360\Lib360::$namespace . '\\' . \spoof\lib360\Lib360::$autoloadNamespace . '\\' . \spoof\lib360\Lib360::$autoloadClass;
		$this->assertTrue(class_exists($autoloadClassName, FALSE), "Defined autoload class " . $autoloadClassName . " didn't load");
	}

	/**
	*	@covers \spoof\lib360\Lib360::initialize
	*	@depends testInitialize_AutoloadClass
	*/
	public function testInitialize_AutoloadImport()
	{
		\spoof\lib360\Lib360::reset();
		\spoof\lib360\Lib360::initialize();
		$directory = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
		$dirsProperty = $this->getProtectedProperty('\spoof\lib360\autoload\Autoload', 'dirs');
		$this->assertArrayHasKey($directory, $dirsProperty->getValue(), "import method didn't register given directory");
	}

	/**
	*	@covers \spoof\lib360\Lib360::initialize
	*	@depends testInitialize_AutoloadClass
	*/
	public function testInitialize_InitializedVariable()
	{
		$p = $this->getProtectedProperty('\spoof\lib360\Lib360', 'initialized');
		$this->assertTrue($p->getValue(), "Failed to set initialized property to true during initialization");
	}

	/**
	*	@covers \spoof\lib360\Lib360::reset
	*	@depends testInitialize_InitializedVariable
	*/
	public function testReset_InitializedVariable()
	{
		\spoof\lib360\Lib360::reset();
		$p = $this->getProtectedProperty('\spoof\lib360\Lib360', 'initialized');
		$this->assertFalse($p->getValue(), "Failed to set initialized property to true during initialization");
	}

	/**
	*	@covers \spoof\lib360\Lib360::initialize
	*	@depends testReset_InitializedVariable
	*/
	public function testInitialize_SPLExtensions()
	{
		$spl_extensions = '.php';
		\spoof\lib360\Lib360::reset();
		\spoof\lib360\Lib360::$autoloadExtensions = $spl_extensions;
		\spoof\lib360\Lib360::initialize();
		$this->assertEquals($spl_extensions, spl_autoload_extensions(), "initialize function didn't register the SPL extensions it has defined");
	}

	/**
	*	@covers \spoof\lib360\Lib360::initialize
	*	@depends testInitialize_SPLExtensions
	*/
	public function testInitialize_SPLExtensionsBackup()
	{
		$spl_original_extensions = '.inc';
		$spl_new_extensions = '.php';
		\spoof\lib360\Lib360::reset();
		spl_autoload_extensions($spl_original_extensions);
		\spoof\lib360\Lib360::$autoloadExtensions = $spl_new_extensions;
		\spoof\lib360\Lib360::initialize();
		$p = $this->getProtectedProperty('\spoof\lib360\Lib360', 'autoloadExtensionsBackup');
		$this->assertEquals($spl_original_extensions, $p->getValue(), "Failed to make an internal backup of original SPL extensions");
	}

	/**
	*	@covers \spoof\lib360\Lib360::reset
	*	@depends testInitialize_SPLExtensionsBackup
	*/
	public function testReset_SPLExtensions()
	{
		$spl_original_extensions = '.inc';
		$spl_new_extensions = '.php';
		\spoof\lib360\Lib360::reset();
		spl_autoload_extensions($spl_original_extensions);
		\spoof\lib360\Lib360::$autoloadExtensions = $spl_new_extensions;
		\spoof\lib360\Lib360::initialize();
		\spoof\lib360\Lib360::reset();
		$this->assertEquals($spl_original_extensions, spl_autoload_extensions(), "Failed resetting SPL autoload extensions");
	}

	/**
	*	@covers \spoof\lib360\Lib360::initialize
	*	@depends testInitialize_AutoloadClass
	*/
	public function testInitialize_SPLAutoload()
	{
		\spoof\lib360\Lib360::initialize();
		$functions = spl_autoload_functions();
		$found = FALSE;
		foreach ($functions as $function_entry)
		{
			if (is_array($function_entry) && $function_entry[0] == \spoof\lib360\Lib360::$namespace . '\\' . \spoof\lib360\Lib360::$autoloadNamespace . '\\' . \spoof\lib360\Lib360::$autoloadClass && $function_entry[1] == \spoof\lib360\Lib360::$autoloadMethod)
			{
				$found = TRUE;
				break;
			}
		}
		$this->assertTrue($found, "initialize failed to register its own defined SPL autoload " . \spoof\lib360\Lib360::$namespace . '\\' . \spoof\lib360\Lib360::$autoloadNamespace . '\\' . \spoof\lib360\Lib360::$autoloadClass . "::" . \spoof\lib360\Lib360::$autoloadMethod);
	}

}

?>
