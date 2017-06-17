<?php

namespace spoof\lib360\db\data;

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
 * An abstract database table view class.
 *
 * Provides ability to create logical views across 2 or more database tables.
 * The actual extending classes need to provide the view name, connection
 * alias name, default fields, and table join configuration.
 */
abstract class View extends Table implements IView
{

    /**
     * Array of joins for the view.
     *
     * Extending classes should define table joins for the implemented view.
     * Possible types are \spoof\lib360\db\join\IJoin, ITable, or string table
     * name.
     *
     * @see \spoof\lib360\db\join\Join
     * @see Table
     */
    public $joins = array();

}

?>
