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

namespace spoof\lib360\api\request\Handler;

use spoof\lib360\api;

/**
 * REST API handler class
 */
class Rest extends api\request\Handler
{

    /**
     * Internal map of response statuses to HTTP response codes
     */
    protected $statusMap = array(
        api\Response::STATUS_OK => '200 OK',
        api\Response::STATUS_OK_CREATED => '201 Created',
        api\Response::STATUS_BAD_REQUEST => '400 Bad Request',
        api\Response::STATUS_AUTH_FAILED => '401 Unauthorized',
        api\Response::STATUS_RESOURCE_NOT_FOUND => '404 Not Found',
        api\Response::STATUS_METHOD_NOT_ALLOWED => '405 Method Not Allowed',
        api\Response::STATUS_ERROR => '500 Internal Server Error',
        api\Response::STATUS_METHOD_NOT_IMPLEMENTED => '501 Not Implemented',
    );

    /**
     * Generate request object from an HTTP request
     *
     * @return api\Request request object
     */
    public function getRequest()
    {
        $request = new api\Request();
        if (strpos($_SERVER['REQUEST_URI'], '?') === false) {
            $request->parts = explode('/', $_SERVER['REQUEST_URI']);
        } else {
            $request->parts = explode('/', strstr($_SERVER['REQUEST_URI'], '?', true));
        }
        // Remove part 0 generated as a result of initial `/`
        if (isset($request->parts[0]) && $request->parts[0] == '') {
            array_shift($request->parts);
        }
        $request->operation = $_SERVER['REQUEST_METHOD'];
        $request->data = $_GET;
        if ($request->operation == 'POST') {
            $request->data += $_POST;
        } elseif ($request->operation == 'PUT') {
            parse_str(file_get_contents('php://input'), $_PUT);
            $request->data += $_PUT;
        }
        return $request;
    }

    /**
     * Output a response to the client
     *
     * @param api\Response $response response object
     *
     * @return boolean true
     */
    public function sendResponse(api\Response $response)
    {
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ?
            $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        $body = null;
        switch ($response->status) {
            case api\Response::STATUS_OK:
            case api\Response::STATUS_BAD_REQUEST:
            case api\Response::STATUS_RESOURCE_NOT_FOUND:
            case api\Response::STATUS_METHOD_NOT_ALLOWED:
            case api\Response::STATUS_ERROR:
            case api\Response::STATUS_METHOD_NOT_IMPLEMENTED:
                $body = $response->body;
            default:
                $header = $protocol . ' ' . $this->statusMap[$response->status];
        }

        // output response
        header($header);
        header('Content-Type: application/json');
        if (isset($body)) {
            echo json_encode(
                array(
                    'status' => $response->status,
                    'message' => $body,
                )
            );
        }

        return true;
    }

}

?>
