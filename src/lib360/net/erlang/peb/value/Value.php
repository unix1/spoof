<?php

namespace spoof\lib360\net\erlang\peb\value;

class Value implements IValue
{

    public $type;
    public $value;

    public function __construct($value, $type)
    {
        $this->type = $type;
        $this->value = $value;
    }

}

?>
