<?php

namespace spoof\lib360\db\data;

use spoof\lib360\db\connection\Pool;

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
 * Abstract class that implements database storage interface
 */
abstract class Store implements IStore
{
    /**
     * Data store name
     *
     * Extending classes will need to set the actual data store or table name.
     * This name will be set as a value of the type attribute of the record
     * object returned from this class.
     */
    protected $name;

    /**
     * Database connection alias.
     * Extending classes will need to set the actual database connection alias
     * for use with the connection pooling implementation.
     */
    protected $db;

    /**
     * Optional query executor name
     *
     * If specified this will override the executor provided by the connection
     * driver.
     */
    protected $executor;

    /**
     * Optional database query language.
     *
     * If specified this will override the language specified with the
     * connection driver.
     */
    protected $language;

    /**
     * Returns the name of the table.
     *
     * @return string table name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the table.
     *
     * @param string $name table name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the connection alias of the table.
     *
     * @return string connection alias
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * Set the database connection alias of the table.
     *
     * @param string $db database alias
     */
    public function setDB($db)
    {
        $this->db = $db;
    }

    /**
     * Returns name of the query executor.
     *
     * The driver executor is returned, unless it is overriden by the storage object.
     *
     * @return \spoof\lib360\db\executor\IExecutor object
     */
    public function getExecutor()
    {
        if (!is_null($this->executor)) {
            $executor = $this->executor;
        } else {
            $executor = Pool::getByName($this->db)->driver->executor;
        }
        return $executor;
    }

    /**
     * Returns name of the query language.
     *
     * The driver language is returned, unless it is overriden by the storage
     * object.
     *
     * @return \spoof\lib360\db\language\ILanguage object
     */
    public function getLanguage()
    {
        if (!is_null($this->language)) {
            $language = $this->language;
        } else {
            $language = Pool::getByName($this->db)->driver->language;
        }
        return $language;
    }

}

?>
