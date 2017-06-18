<?php

/**
 *  This is Spoof.
 *  Copyright (C) 2011-2017  Spoof project.
 *
 *  Spoof is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Spoof is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Spoof.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace spoof\lib360\db\data;

/**
 * Static factory class for loading tables on-the-fly
 */
class TableFactory
{
    /**
     * Internal object cache for table objects
     */
    protected static $cache = array();

    /**
     * Creates and returns a new instance of Table object, or retrieves it
     * from cache if previously accessed.
     *
     * @param string $db database alias
     * @param string $table table name
     *
     * @return Table object
     */
    public static function get($db, $table)
    {
        if (!(isset(self::$cache[$db]) && isset(self::$cache[$db][$table]))) {
            $tbl = new Table();
            $tbl->setDB($db);
            $tbl->setName($table);
            self::cache($tbl);
        }
        return self::$cache[$db][$table];
    }

    /**
     * Caches supplied table object.
     *
     * @param IStore $table object to cache
     */
    public static function cache(IStore $table)
    {
        self::$cache[$table->getDB()][$table->getName()] = $table;
    }

}

?>
