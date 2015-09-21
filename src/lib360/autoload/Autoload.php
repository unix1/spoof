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
*	Namespace for autoloading classes
*
*	Provides implementation for spl_autoload_register in PHP
*/
namespace spoof\lib360\autoload;

/**
*	Autoload class
*
*	Provides flexible namespace-based loading rules for classes from
*	multiple base directories.
*/
class Autoload
{
	/**
	*	Internal storage for base directories
	*/
	protected static $dirs = array();

	/**
	*	Imports directory.
	*
	*	Adds given directory to current list of base directories.
	*
	*	@param string $directory directory to add
	*/
	public static function import($directory)
	{
		if (!isset(self::$dirs[$directory]))
		{
			self::$dirs[$directory] = $directory;
		}
	}

	/**
	*	Removes directory.
	*
	*	Removes given directory from current list of base directories.
	*
	*	@param string $directory directory to remove
	*/
	public static function remove($directory)
	{
		if (isset(self::$dirs[$directory]))
		{
			unset(self::$dirs[$directory]);
		}
	}

	/**
	*	Loads given class.
	*
	*	Attempts to load given class. The class name has to contain full
	*	namespace path. This function will attempt to resolve the class path
	*	starting from each directory set with the import function. The first
	*	match will load the file and stop.
	*
	*	@param string $class full class path including namespace
	*/
	public static function load($class)
	{
		$loaded = FALSE;
		$extensions = explode(',', spl_autoload_extensions());
		$classpath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
		foreach (self::$dirs as $dir)
		{
			foreach ($extensions as $ext)
			{
				$filename = $dir . $classpath . $ext;
				if (file_exists($filename) && is_readable($filename))
				{
					require($filename);
					$loaded = TRUE;
					break;
				}
			}
			if ($loaded)
			{
				break;
			}
		}
		if (!$loaded)
		{
			error_log(__METHOD__ . ": could not load class: $class");
		}
	}

}

?>
