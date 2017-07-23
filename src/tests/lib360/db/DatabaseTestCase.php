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

namespace spoof\tests\lib360\db;

use spoof\lib360\db\connection\Config;
use spoof\lib360\db\connection\PDO;
use spoof\lib360\db\connection\Pool;

abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * PDO object, instantiated once per test
     *
     * @var \PDO
     */
    protected static $pdo = null;

    /**
     * PHPUnit's database connection, instantiated once per test
     *
     * @var \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $conn = null;

    protected static $tablesCreated = false;
    protected static $db = 'test';

    public function getDataSet()
    {
        if (!self::$tablesCreated) {
            self::$pdo->query('drop table if exists "user"');
            self::$pdo->query(
                'create table user (
                    id integer primary key autoincrement,
                    date_created datetime null default null,
                    name_first varchar(50),
                    name_last varchar(50),
                    status varchar(10) not null default \'\'
                )'
            );
            self::$tablesCreated = true;
        }
        return $this->createXMLDataSet(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test-data1.xml');
    }

    public function setUp()
    {
        parent::setUp();
        Pool::add(new PDO(new Config($GLOBALS['DB_DSN'])), self::$db);
    }

    public function tearDown()
    {
        parent::tearDown();
        Pool::removeByName('test');
    }

    public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new \PDO($GLOBALS['DB_DSN']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo);
        }
        return $this->conn;
    }
}

?>
