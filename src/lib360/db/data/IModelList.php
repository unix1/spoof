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

interface IModelList extends \Iterator, \ArrayAccess, \Countable
{

    /**
     * Constructor, sets record list
     *
     * @param IRecordList $recordlist
     * @param string $modelClass Model class used for instantiating model objects during iteration
     */
    public function __construct(IRecordList $recordlist, $modelClass);

    /**
     * Exports model list to array representation.
     *
     * @return array array of associative arrays with field names as indexes
     */
    public function toArray();

}

?>
