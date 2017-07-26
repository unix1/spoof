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
 * Database join class.
 * This class describes an database table join between 2 or more database tables.
 */
class Join implements IJoin
{
    /**
     * Constant for left outer join
     */
    const JOIN_TYPE_LEFT_OUTER = 1;
    /**
     * Constant for inner join
     */
    const JOIN_TYPE_INNER = 2;
    /**
     * Constant for default join
     */
    const JOIN_TYPE_JOIN = 3;
    /**
     * Constant for right outer join
     */
    const JOIN_TYPE_RIGHT_OUTER = 4;
    /**
     * Constant for full join
     */
    const JOIN_TYPE_FULL = 5;
    /**
     * Base table name
     *
     * Property used to store name of the base table for the join.
     */
    public $tableBase;
    /**
     * Array of joined table names
     *
     * Property used to store names of joined tables.
     */
    public $tableJoin;
    /**
     * Array of join types
     *
     * Property used to store types of joins associated with joined tables.
     */
    public $type;
    /**
     * Array of database conditions
     *
     * Property used to store database conditions or condition groups used for joined tables.
     */
    public $condition;

    /**
     * Constructor.
     *
     * @param string $tableBase string base table name
     * @param integer $type join type, one of defined class join type constants should be used
     * @param string $tableJoin joined table name
     * @param ICondition $condition database condition object
     */
    public function __construct($tableBase, $type, $tableJoin, ICondition $condition)
    {
        $this->tableBase = $tableBase;
        $this->tableJoin = array();
        $this->type = array();
        $this->condition = array();
        $this->tableJoin[] = $tableJoin;
        $this->type[] = $type;
        $this->condition[] = $condition;
    }

    /**
     * Adds a table to the join.
     *
     * @param integer $type join type, one of defined class join type constants should be used
     * @param string $tableJoin joined table name
     * @param ICondition $condition database condition object
     */
    public function addTable($type, $tableJoin, ICondition $condition)
    {
        $this->tableJoin[] = $tableJoin;
        $this->type[] = $type;
        $this->condition[] = $condition;
    }

}

?>
