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

/**
 * Abstract handler class for requests
 *
 * Implementing handlers should provide means of parsing/responding/dealing with
 * requests and responses from their respective environments.
 */
abstract class Handler implements IHandler
{

    /**
     * Internal storage for authenticator
     * @var auth\IAuthenticator
     */
    protected $authenticator;

    /**
     * Internal storage for request router
     * @var api\IRouter
     */
    protected $router;

    /**
     * Sets the authenticator
     *
     * @param auth\IAuthenticator $authenticator authenticator object
     */
    public function setAuthenticator(auth\IAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Sets the application router
     *
     * @param api\IRouter $router request router object
     */
    public function setRouter(api\IRouter $router)
    {
        $this->router = $router;
    }

    /**
     * Main method that handles the client request logic
     * - authenticates via the authenticator
     * - parses the request into a request object
     * - calls the router that handles the request object
     * - sends the response to the client
     */
    public function handle()
    {
        $request = $this->getRequest();
        if ($this->authenticate($request)) {
            try {
                $response = $this->router->handleRequest($request);
            } catch (\BadMethodCallException $e) {
                $response = new api\Response();
                $response->status = api\Response::STATUS_METHOD_NOT_IMPLEMENTED;
                $response->body = $e->getMessage();
            } catch (\Exception $e) {
                $response = new api\Response();
                $response->status = api\Response::STATUS_ERROR;
                $response->body = $e->getMessage();
            }
        } else {
            $response = new api\Response();
            $response->status = api\Response::STATUS_AUTH_FAILED;
        }
        $this->sendResponse($response);
    }

    /**
     * Authenticates the request via the authenticator
     *
     * @param api\Request $request request object
     *
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function authenticate(api\Request $request)
    {
        return $this->authenticator->authenticate($request);
    }

}

?>
