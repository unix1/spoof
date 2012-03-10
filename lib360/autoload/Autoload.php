<?php

namespace lib360\autoload;

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

class Autoload
{

	protected static $dirs = array();

	public static function import($directory)
	{
		if (!isset(self::$dirs[$directory]))
		{
			self::$dirs[$directory] = $directory;
		}
	}

	public static function remove($directory)
	{
		if (isset(self::$dirs[$directory]))
		{
			unset(self::$dirs[$directory]);
		}
	}

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
					require_once($filename);
					$loaded = TRUE;
					break;
				}
			}
			if ($loaded)
			{
				break;
			}
		}
	}

}

?>