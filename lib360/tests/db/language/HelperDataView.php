<?php

namespace lib360\tests\db\language;

/*
    This is Spoof.
    Copyright (C) 2011-2012  Spoof project.

    Spoof is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Spoof is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Spoof.  If not, see <http://www.gnu.org/licenses/>.
 */

class HelperDataView extends \lib360\db\data\View
{

	public function __construct()
	{
		$this->name = 'commentsview';
		$table1_base = 'comments';
		$join1_type1 = \lib360\db\join\Join::JOIN_TYPE_INNER;
		$table1_join1 = 'user';
		$cond11 = new \lib360\db\condition\Condition(
							new \lib360\db\value\Value($table1_base . '.user_id', \lib360\db\value\Value::TYPE_COLUMN),
							\lib360\db\condition\Condition::OPERATOR_EQUALS,
							new \lib360\db\value\Value($table1_join1 . '.id', \lib360\db\value\Value::TYPE_COLUMN)
					);
		$join1_type2 = \lib360\db\join\Join::JOIN_TYPE_LEFT_OUTER;
		$table1_join2 = 'group';
		$cond12 = new \lib360\db\condition\Condition(
							new \lib360\db\value\Value($table1_join1 . '.group_id', \lib360\db\value\Value::TYPE_COLUMN),
							\lib360\db\condition\Condition::OPERATOR_EQUALS,
							new \lib360\db\value\Value($table1_join2 . '.id', \lib360\db\value\Value::TYPE_COLUMN)
					);
		$j1 = new \lib360\db\join\Join($table1_base, $join1_type1, $table1_join1, $cond11);
		$j1->addTable($join1_type2, $table1_join2, $cond12);

		$table2_base = new HelperTable();
		$table3_base = 'test_table3';

		$this->joins[] = $j1;
		$this->joins[] = $table2_base;
		$this->joins[] = $table3_base;
	}

}

?>