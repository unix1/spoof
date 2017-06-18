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

namespace spoof\lib360\db\connection;

/**
 *    Database connection pool interface.
 *    This interface is used to define database connection pool.
 */
interface IPool
{

    /**
     * Adds the specified connection to the connection pool and assigns the
     * specified alias.
     *
     * @param IConnection $conn database connection object
     * @param string $name name alias for the connection
     *
     * @throw \InvalidArgumentException when supplied alias already exists
     */
    public static function add(IConnection $conn, $name);

    /**
     * Retrieves previously stored database connection object by its alias.
     *
     * @param string $name connection name
     * @param boolean $connect if TRUE and not yet connected the database
     *    connection will be attempted, default TRUE
     *
     * @return Connection object
     *
     * @throw \InvalidArgumentException when supplied alias is not found in the pool
     */
    public static function getByName($name, $connect = true);

    /**
     * Closes all active connections in the pool.
     */
    public static function closeAll();

}

?>
