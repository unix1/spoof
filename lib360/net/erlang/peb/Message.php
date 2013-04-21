<?php

namespace lib360\net\erlang\peb;

class Message
{

	public $value;

	public function __construct(value\Value $value)
	{
		$this->value = $value;
	}

	public function getPebArgs()
	{
		list($format, $values) = $this->generatePebArgs($this->value);
		return array($format, array($values));
	}

	protected function generatePebArgs(value\Value $value)
	{
		$format = '';
		$values = '';
		if ($value instanceof value\Collection)
		{
			$values = array();
			$start = value\Type::$format[$value->type]['start'];
			$end = value\Type::$format[$value->type]['end'];
			$separator = value\Type::$format[$value->type]['separator'];
			$format .= $start;
			for ($i = 0; $i < count($value->value); ++$i)
			{
				list($f, $v) = $this->generatePebArgs($value->value[$i]);
				($i == 0) ? $format .= $f : $format .= $separator . $f;
				$values[] = $v;
			}
			$format .= $end;
		}
		elseif ($value instanceof value\Primitive)
		{
			$format = value\Type::$format[$value->type];
			$values = $value->value;
		}
		else
		{
			throw InvalidArgumentException("Unexpected value type: " . get_class($value) . " is not either value\Collection or value\Primitive");
		}

		return array($format, $values);
	}

}

?>