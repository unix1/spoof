<?php

namespace spoof\lib360\db\query;

/**
 *  This is Spoof.
 *  Copyright (C) 2011-2012  Spoof project.
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

/**
 * Database query interface
 */
interface IQuery
{
    /**
     * Constructor
     *
     * @param string $query initial query string, optional, default NULL
     * @param array $values initial values associative array, optional, default NULL
     */
    public function __construct($query = null, array $values = null);

    /**
     * Adds query object to current query object.
     *
     * @param IQuery $query object to add
     */
    public function addQuery(IQuery $query);

    /**
     * Adds string to query object.
     *
     * @param string $query string to add
     * @param boolean $hintSpace hints use of space prior to appending, optional, default TRUE
     */
    public function addString($query, $hintSpace = true);

    /**
     * Adds values array to query object.
     *
     * @param array $values array to append
     */
    public function addValues(array $values);

    /**
     * Sets string query value.
     *
     * @param string $query string to set
     */
    public function setString($query);

}

?>
