<?php

namespace spoof\lib360\db\condition;

use \spoof\lib360\db\value;

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
*	Simple DB condition class.
*
*	This class is used to describe a simple database condition that uses
*	the left side, operator, and the right side to evaluate the statement.
*/
class Condition implements ICondition
{
	/**
	*	Value of the left side of the condition
	*
	*	Property used to store the value of the left side of the database condition.
	*/
	public $value1;

	/**
	*	Condition operator
	*
	*	Property used to store the operator for use with the database condition.
	*/
	public $operator;

	/**
	*	Value of the right side of the condition
	*
	*	Property used to store the value of the right side of the database condition.
	*/
	public $value2;

	/**
	*	Constant for equals operator
	*/
	const OPERATOR_EQUALS = 1;

	/**
	*	Constant for not equals operator
	*/
	const OPERATOR_NOT_EQUALS = -1;

	/**
	*	Constant for greater than operator
	*/
	const OPERATOR_GREATER_THAN = 2;

	/**
	*	Constant for greater than or equals operator
	*/
	const OPERATOR_GREATER_THAN_OR_EQUAL = 3;

	/**
	*	Constant for in operator
	*/
	const OPERATOR_IN = 4;

	/**
	*	Constant for less than operator
	*/
	const OPERATOR_LESS_THAN = -2;

	/**
	*	Constant for less than or equals operator
	*/
	const OPERATOR_LESS_THAN_OR_EQUAL = -3;

	/**
	*	Constant for not in operator
	*/
	const OPERATOR_NOT_IN = -4;

	/**
	*	Constructor.
	*
	*	Instantiates a database condition object with the given
	*	left side value, operator, right side value.
	*
	*	@param value\IValue $value1 the left side of the condition
	*	@param string $operator database condition operator
	*	@param value\IValue $value2 the right side of the condition
	*/
	public function __construct(value\IValue $value1, $operator, value\IValue $value2)
	{
		if (($operator == self::OPERATOR_IN || $operator == self::OPERATOR_NOT_IN) &&
			$value2->getType() != value\Value::TYPE_ARRAY)
		{
			throw new \InvalidArgumentException("Invalid second operand. OPERATOR_IN expects \lib360\db\Value::TYPE_ARRAY (" . value\Value::TYPE_ARRAY . "); " . $value2->getType() . " given.");
		}
		$this->value1 = $value1;
		$this->operator = $operator;
		$this->value2 = $value2;
	}

}

?>
