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

use spoof\lib360\db\condition\ICondition;

/**
 * Data table interface extends data storage interface.
 * Data table implementations must be based on this interface.
 */
interface ITable extends IStore
{
    /**
     * Gets table records that match the supplied database condition.
     *
     * @param ICondition $condition optional condition
     *    to apply to the query
     * @param array $values optional associative array of values for aliases
     *    in the condition object
     * @param array $fields optional array of fields to return, can be
     *    associative for (table field) => (select as field) or a simple array
     *    of table field names, will override default $fields property
     *
     * @return IRecordList a database recordlist object, @see RecordList
     *    TODO add order/group by support
     */
    public function select(
        ICondition $condition = null,
        array $values = null,
        array $fields = null
    );

    /**
     * Gets table records by field criteria.
     *
     * @param array $conditions optional associative array of column names
     *    and their values to use as conditions, values will explicitly be cast
     *    as strings
     * @param array $fields optional array of fields to return, can be
     *    associative for (table field) => (select as field) or a simple array
     *    of table field names, will override default $fields property
     *
     * @return IRecordList object containing matched rows @see RecordList
     */
    public function selectRecords(
        array $conditions = array(),
        array $fields = null
    );

    public function selectRecord($id, array $fields = null);

    /**
     * Updates database record(s) based on supplied criteria and values.
     *
     * @param array $fields associative array of fields to update
     *    (table field) => (update value)
     * @param ICondition $condition optional ICondition object to apply to the
     *    update
     * @param array $values optional associative array of values for aliases
     *    in the condition object
     *
     * @return integer number of rows updated
     */
    public function update(
        array $fields,
        ICondition $condition = null,
        array $values = array()
    );

    /**
     * Stores record modifications in the table by record's primary key.
     *
     * @param IRecord $record
     *
     * @return int Number of rows updated
     *
     * @throws RecordNotFoundException when record to update is not found
     */
    public function updateRecord(IRecord $record);

    /**
     * Inserts a database record.
     *
     * @param array $fields associative array of fields for insert
     *    (table field) => (update value)
     *
     * @return mixed inserted row ID
     */
    public function insert(array $fields);

    /**
     * Inserts a database record.
     *
     * @param IRecord $record
     *
     * @return mixed inserted row ID
     */
    public function insertRecord(IRecord $record);

    /**
     * Deletes table records that match the supplied database condition.
     *
     * @param ICondition $condition optional condition to apply to the query
     * @param array $values optional associative array of values for aliases
     *    in the condition object
     *
     * @return integer number of rows deleted
     */
    public function delete(ICondition $condition = null, array $values = array());

    /**
     * Deletes a record from the table.
     *
     * @param IRecord $record
     *
     * @return integer number of rows deleted
     *
     * @throws RecordNotFoundException when record to delete is not found
     */
    public function deleteRecord(IRecord $record);

}

?>
