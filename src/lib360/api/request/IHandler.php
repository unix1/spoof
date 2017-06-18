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

namespace spoof\lib360\api\request;

use spoof\lib360\api;
use spoof\lib360\auth;

interface IHandler
{

    /**
     * Sets the authenticator
     *
     * @param auth\IAuthenticator $authenticator authenticator object
     */
    public function setAuthenticator(auth\IAuthenticator $authenticator);

    /**
     * Sets the application router
     *
     * @param api\IRouter $router request router object
     */
    public function setRouter(api\IRouter $router);

    /**
     * Authenticates the request via the authenticator
     *
     * @param api\Request $request request object
     *
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function authenticate(api\Request $request);

    /**
     * Main method that handles the client request logic
     * - authenticates via the authenticator
     * - parses the request into a request object
     * - calls the application backend that handles the request object
     * - sends the response to the client
     */
    public function handle();

    /**
     * Generate request object from an HTTP request
     *
     * @return api\Request request object
     */
    public function getRequest();

    /**
     * Output a response to the client
     *
     * @param api\Response $response response object
     */
    public function sendResponse(api\Response $response);

}

?>
