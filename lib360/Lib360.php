<?php

namespace lib360;

/*
    This is Spoof.
    Copyright (C) 2011  Spoof project.

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

class Lib360
{

	//public static $autoloadExtensions = '.php';
	public static $autoloadExtensions = NULL;
	public static $autoloadNamespace = 'autoload';
	public static $autoloadClass = 'Autoload';
	public static $autoloadMethod = 'load';
	public static $namespace = __NAMESPACE__;
	protected static $initialized = FALSE;
	protected static $autoloadExtensionsBackup = NULL;
	protected static $autoloadExtensionsOverriden = FALSE;

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