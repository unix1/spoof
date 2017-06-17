<?php

namespace spoof\lib360\auth;

use spoof\lib360\api;

/**
 * Request authenticator that allows all requests
 */
class AllowAll implements IAuthenticator
{

    /**
     * Authenticate a request
     *
     * @param \lib\Web\Request $request Request object
     *
     * @return boolean TRUE
     */
    public function authenticate(api\Request $request)
    {
        return true;
    }

}

?>
