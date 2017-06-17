<?php

namespace spoof\lib360\net\erlang\peb\value;

class Primitive extends Value
{

    public function __construct($value, $type)
    {
        /**
         * @todo add type/value combination validation
         */
        parent::__construct($value, $type);
    }

}

?>
