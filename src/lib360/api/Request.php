<?php

namespace spoof\lib360\api;

/**
* Standard request object
*/
class Request
{

	/**
	* Parts of request path
	*/
	public $parts = array();

	/**
	* Operation to perform
	*/
	public $operation;

	/**
	* Data or arguments passed in request
	*/
	public $data = array();

}

?>
