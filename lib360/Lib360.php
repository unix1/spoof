<?php

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

/**
*	Main spoof library namespace
*/
namespace lib360;

/**
*	Provides functions to initialize and reset lib360.
*/
class Lib360
{

	//public static $autoloadExtensions = '.php';
	/**
	*	Override default spl_autoload_extensions
	*
	*	Setting this property will override the default extensions and will
	*	allow Autoload class to try files with the extensions listed here
	*	in that order. Syntax is exactly the same as spl_autoload_extensions.
	*	@see Autoload
	*	@see spl_autoload_extensions
	*/
	public static $autoloadExtensions = NULL;

	/**
	*	Namespace of autoload class
	*
	*	Use this to implement custom autoloader.
	*/
	public static $autoloadNamespace = 'autoload';

	/**
	*	Autoload class name
	*
	*	Use this to implement custom autoloader.
	*/
	public static $autoloadClass = 'Autoload';

	/**
	*	Autoload class method
	*
	*	Use this to implement custom autoloader.
	*/
	public static $autoloadMethod = 'load';

	/**
	*	Current namespace
	*	@todo determine whether this is needed
	*/
	public static $namespace = __NAMESPACE__;

	/**
	*	Internal initialization state
	*/
	protected static $initialized = FALSE;

	/**
	*	Internal autoload extensions backup
	*/
	protected static $autoloadExtensionsBackup = NULL;

	/**
	*	Internal record of whether autoload extensions were overriden
	*/
	protected static $autoloadExtensionsOverriden = FALSE;

	/**
	*	Initialize lib360
	*
	*	Includes and registers the autoload class, backs up previous autoload
	*	values, sets lib360 configuration.
	*/
	public static function initialize()
	{
		if (!self::$initialized)
		{
			require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$autoloadNamespace . DIRECTORY_SEPARATOR . self::$autoloadClass . '.php');
			$autoloadClass = '\\' . self::$namespace . '\\' . self::$autoloadNamespace . '\\' . self::$autoloadClass;
			$autoloadClass::import(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
			spl_autoload_register($autoloadClass . '::' . self::$autoloadMethod);
			self::$autoloadExtensionsBackup = spl_autoload_extensions();
			if (!is_null(self::$autoloadExtensions))
			{
				spl_autoload_extensions(self::$autoloadExtensions);
				self::$autoloadExtensionsOverriden = TRUE;
			}
			self::$initialized = TRUE;
		}
	}

	/**
	*	Resets lib360 configuration
	*
	*	Attempts to restore configuration prior to lib360 initilaization.
	*	Specifically, restores backed up autoload extensions.
	*/
	public static function reset()
	{
		if (self::$autoloadExtensionsOverriden)
		{
			spl_autoload_extensions(self::$autoloadExtensionsBackup);
		}
		self::$initialized = FALSE;
	}

}

?>