<?php

namespace spoof\lib360\api;

use spoof\lib360\api;

/**
* Application service interface that handles the request object
*/
interface IRouter
{

	/**
	* Handles API request and returns API response
	*
	* @param api\Request $request request object
	*
	* @return api\Response response object
	*/
	public function handleRequest(api\Request $request);

}

?>
