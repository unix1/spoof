<?php

namespace spoof\lib360\Auth;

use \spoof\lib360\api;

/**
* Authentication interface
*
* Request authenticators must implement this interface
*/
interface IAuthenticator
{

	/**
	* Authenticate a request
	*
	* @param \lib\Web\Request $request Request object
	*
	* @return boolean TRUE on success, FALSE otherwise
	*/
	public function authenticate(api\Request $request);

}

?>
