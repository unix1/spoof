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
 * This interface defines database storage.
 */
interface IStore
{
    /**
     * Returns the name of the table.
     *
     * @return string table name
     */
    public function getName();

    /**
     * Returns the connection alias of the table.
     *
     * @return string connection alias
     */
    public function getDB();

    /**
     * Set the name of the table.
     *
     * @param string $name table name
     */
    public function setName($name);

    /**
     * Set the database connection alias of the table.
     *
     * @param string $db database alias
     */
    public function setDB($db);

    /**
     * Returns name of the query executor.
     *
     * The driver executor is returned, unless it is overriden by the storage
     * object.
     *
     * @return \spoof\lib360\db\executor\IExecutor object
     */
    public function getExecutor();

    /**
     * Returns name of the query language.
     *
     * The driver language is returned, unless it is overriden by the storage
     * object.
     *
     * @return \spoof\lib360\db\language\ILanguage object
     */
    public function getLanguage();

}

?>
