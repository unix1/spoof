<?php

namespace lib360\net\erlang\peb\value;

class Collection extends Value
{

	public function __construct($value, $type)
	{
		/**
		*	@todo add type/value combination validation
		*/
		parent::__construct($value, $type);
	}

}

?>