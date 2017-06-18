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

namespace spoof\lib360\db\join;

use spoof\lib360\db\condition\ICondition;

/**
 *    Interface for database join implementations.
 */
interface IJoin
{
    /**
     * Constructor.
     *
     * @param string $table_base string base table name
     * @param integer $type join type, one of defined class join type constants should be used
     * @param string $table_join joined table name
     * @param ICondition $condition database condition object
     */
    public function __construct($table_base, $type, $table_join, ICondition $condition);

    /**
     * Adds a table to the join.
     *
     * @param integer $type join type, one of defined class join type constants should be used
     * @param string $table_join joined table name
     * @param ICondition $condition database condition object
     */
    public function addTable($type, $table_join, ICondition $condition);

}

?>
