<?php

namespace lib360\tests;

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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Lib360.php');

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
	*	@covers \lib360\Lib360::initialize
	*/
	public function testInitialize_AutoloadClass()
	{
		\lib360\Lib360::reset();
		\lib360\Lib360::initialize();
		$autoloadClassName = \lib360\Lib360::$namespace . '\\' . \lib360\Lib360::$autoloadNamespace . '\\' . \lib360\Lib360::$autoloadClass;
		$this->assertTrue(class_exists($autoloadClassName, FALSE), "Defined autoload class " . $autoloadClassName . " didn't load");
	}

	/**
	*	@covers \lib360\Lib360::initialize
	*	@depends testInitialize_AutoloadClass
	*/
	public function testInitialize_AutoloadImport()
	{
		\lib360\Lib360::reset();
		\lib360\Lib360::initialize();
		$directory = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR;
		$dirsProperty = $this->getProtectedProperty('\lib360\autoload\Autoload', 'dirs');
		$this->assertArrayHasKey($directory, $dirsProperty->getValue(), "import method didn't register given directory");
	}

	/**
	*	@covers \lib360\Lib360::initialize
	*	@depends testInitialize_AutoloadClass
	*/
	public function testInitialize_InitializedVariable()
	{
		$p = $this->getProtectedProperty('\lib360\Lib360', 'initialized');
		$this->assertTrue($p->getValue(), "Failed to set initialized property to true during initialization");
	}

	/**
	*	@covers \lib360\Lib360::reset
	*	@depends testInitialize_InitializedVariable
	*/
	public function testReset_InitializedVariable()
	{
		\lib360\Lib360::reset();
		$p = $this->getProtectedProperty('\lib360\Lib360', 'initialized');
		$this->assertFalse($p->getValue(), "Failed to set initialized property to true during initialization");
	}

	/**
	*	@covers \lib360\Lib360::initialize
	*	@depends testReset_InitializedVariable
	*/
	public function testInitialize_SPLExtensions()
	{
		$spl_extensions = '.php';
		\lib360\Lib360::reset();
		\lib360\Lib360::$autoloadExtensions = $spl_extensions;
		\lib360\Lib360::initialize();
		$this->assertEquals($spl_extensions, spl_autoload_extensions(), "initialize function didn't register the SPL extensions it has defined");
	}

	/**
	*	@covers \lib360\Lib360::initialize
	*	@depends testInitialize_SPLExtensions
	*/
	public function testInitialize_SPLExtensionsBackup()
	{
		$spl_original_extensions = '.inc';
		$spl_new_extensions = '.php';
		\lib360\Lib360::reset();
		spl_autoload_extensions($spl_original_extensions);
		\lib360\Lib360::$autoloadExtensions = $spl_new_extensions;
		\lib360\Lib360::initialize();
		$p = $this->getProtectedProperty('\lib360\Lib360', 'autoloadExtensionsBackup');
		$this->assertEquals($spl_original_extensions, $p->getValue(), "Failed to make an internal backup of original SPL extensions");
	}

	/**
	*	@covers \lib360\Lib360::reset
	*	@depends testInitialize_SPLExtensionsBackup
	*/
	public function testReset_SPLExtensions()
	{
		$spl_original_extensions = '.inc';
		$spl_new_extensions = '.php';
		\lib360\Lib360::reset();
		spl_autoload_extensions($spl_original_extensions);
		\lib360\Lib360::$autoloadExtensions = $spl_new_extensions;
		\lib360\Lib360::initialize();
		\lib360\Lib360::reset();
		$this->assertEquals($spl_original_extensions, spl_autoload_extensions(), "Failed resetting SPL autoload extensions");
	}

	/**
	*	@covers \lib360\Lib360::initialize
	*	@depends testInitialize_AutoloadClass
	*/
	public function testInitialize_SPLAutoload()
	{
		\lib360\Lib360::initialize();
		$functions = spl_autoload_functions();
		$found = FALSE;
		foreach ($functions as $function_entry)
		{
			if (is_array($function_entry) && $function_entry[0] == \lib360\Lib360::$namespace . '\\' . \lib360\Lib360::$autoloadNamespace . '\\' . \lib360\Lib360::$autoloadClass && $function_entry[1] == \lib360\Lib360::$autoloadMethod)
			{
				$found = TRUE;
				break;
			}
		}
		$this->assertTrue($found, "initialize failed to register its own defined SPL autoload " . \lib360\Lib360::$namespace . '\\' . \lib360\Lib360::$autoloadNamespace . '\\' . \lib360\Lib360::$autoloadClass . "::" . \lib360\Lib360::$autoloadMethod);
	}

}

?>