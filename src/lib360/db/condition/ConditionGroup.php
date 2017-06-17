<?php

namespace spoof\lib360\db\condition;

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
 * Simple DB condition group class.
 * This class is used to describe a simple SQL condition group that is used to
 * merge 2 or more SQL conditions or SQL condition groups themselves by SQL
 * operators.
 * @see Condition
 */
class ConditionGroup implements IConditionGroup
{
    /**
     * Condition operators
     * These should be used by name, e.g. DBConditionGroup::OPERATOR_AND
     */
    const OPERATOR_AND = 1;
    const OPERATOR_OR = 2;
    /**
     * Initial database condition.
     * Property used to store the initial database condition.
     */
    public $condition;
    /**
     * Array of conditions.
     * Property used to store array of additional database conditions to be merged.
     */
    public $conditions;
    /**
     * Array of operators.
     * Property used to store array of database operators associated with
     * additional merged database conditions.
     */
    public $operators;

    /**
     * Constructor.
     * @param ICondition $condition object that will act as an initial condition
     */
    public function __construct(ICondition $condition)
    {
        $this->condition = $condition;
        $this->conditions = array();
        $this->operators = array();
    }

    /**
     * Adds an additional database condition to the group.
     * @param string $operator operator used to merge the additional database condition
     * @param ICondition $condition object being merged
     */
    public function addCondition($operator, ICondition $condition)
    {
        $this->operators[] = $operator;
        $this->conditions[] = $condition;
    }

}

?>
