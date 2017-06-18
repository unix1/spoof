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

namespace spoof\lib360\net\erlang\peb;

class Message
{

    public $value;

    public function __construct(array $values)
    {
        $this->value = $values;
    }

    public function getPebArgs()
    {
        list($format, $values) = $this->generatePebArgs(
            new value\Collection($this->value, value\Type::LLIST)
        );
        return array($format, array($values));
    }

    protected function generatePebArgs(value\Value $value)
    {
        $format = '';
        $values = '';
        if ($value instanceof value\Collection) {
            $values = array();
            $start = value\Type::$format[$value->type]['start'];
            $end = value\Type::$format[$value->type]['end'];
            $separator = value\Type::$format[$value->type]['separator'];
            $format .= $start;
            for ($i = 0; $i < count($value->value); ++$i) {
                list($f, $v) = $this->generatePebArgs($value->value[$i]);
                ($i == 0) ? $format .= $f : $format .= $separator . $f;
                $values[] = $v;
            }
            $format .= $end;
        } elseif ($value instanceof value\Primitive) {
            $format = value\Type::$format[$value->type];
            $values = $value->value;
        } else {
            throw InvalidArgumentException(
                "Unexpected value type: " . get_class($value) . " is not either value\Collection or value\Primitive"
            );
        }

        return array($format, $values);
    }

}

?>
