<?php

namespace spoof\lib360\Auth;

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
	public function authenticate(\spoof\lib360\api\Request $request);

}

?>
