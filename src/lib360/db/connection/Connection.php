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

use spoof\lib360\db\object\Factory;
use spoof\lib360\db\object\NotFoundException;

/**
 * Abstract database connection class
 *
 * Implementing connection classes must extend this and implement the connect
 * method.
 */
abstract class Connection implements IConnection
{

    /**
     * Driver object
     *
     * Used to retrieve connection specific behavior.
     */
    public $driver;
    /**
     * Connection object.
     *
     * Internal property used to store the PHP PDO connection object.
     */
    protected $connection;
    /**
     * Connection configuration object
     *
     * Internal property used to store database connection configuration object.
     */
    protected $config;
    /**
     * Features array
     *
     * Used to specify support for specific features. Extending classes
     * should specify what features and levels they support.
     */
    protected $features = array();

    /**
     * Constructor for the database connection object instantiates the object
     * but does not connect it to a database.
     *
     * @param IConfig $config database connection configuration object
     *
     * @throws ConfigException when DSN specified with $config object has
     *    invalid format
     * @throws NotFoundException when driver specified with $config
     *    cannot be loaded
     */
    public function __construct(IConfig $config)
    {
        $this->config = $config;
        $driverName = $this->parseDriver($config);
        $this->driver = Factory::get(Factory::OBJECT_TYPE_DRIVER, $driverName);
    }

    /**
     * Parses name of the driver class from Config object.
     *
     * @param IConfig $config
     *
     * @return string driver class name
     *
     * @throws ConfigException when driver name cannot be determined
     */
    public function parseDriver(IConfig $config)
    {
        $col = strpos($config->dsn, ':');
        if ($col === false || $col == 0) {
            throw new ConfigException("Invalid DSN ({$config->dsn}); couldn't parse driver name.");
        }
        // get driver name, explicit config option overrides automatic from DSN
        if (isset($config->driver)) {
            $driverName = $config->driver;
        } else {
            $driverName = ucfirst(substr($config->dsn, 0, $col));
        }
        return $driverName;
    }

    /**
     * Checks whether the database connection is active.
     *
     * @return boolean TRUE if connected, boolean FALSE otherwise
     */
    public function isConnected()
    {
        if (is_null($this->connection)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Retrieves the underlying connection object.
     *
     * @return mixed connection object if connected, NULL otherwise
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Closes the connection.
     */
    public function disconnect()
    {
        $this->connection = null;
    }

}

?>
