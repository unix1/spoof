<?php

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
        api\Response::STATUS_RESOURCE_NOT_FOUND => '404 Not Found',
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
        // Remove part 0 genearated as a result of initial `/`
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
            case api\Response::STATUS_ERROR:
                $body = $response->body;
            default:
                $header = $protocol . ' ' . $this->statusMap[$response->status];
        }

        // output response
        header($header);
        if (isset($body)) {
            echo json_encode(array(
                'status' => $response->status,
                'message' => $body,
            ));
        }
    }

}

?>
