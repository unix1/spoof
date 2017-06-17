<?php

namespace spoof\lib360\api;

/**
 * Standard response object
 */
class Response
{

    /**
     * Handy constants for various response statuses
     */
    const STATUS_OK = 1;
    const STATUS_OK_CREATED = 2;
    const STATUS_METHOD_NOT_DEFINED = 3;
    const STATUS_METHOD_NOT_IMPLEMENTED = 4;
    const STATUS_RESOURCE_NOT_FOUND = 5;
    const STATUS_ERROR = 6;
    const STATUS_AUTH_FAILED = 7;
    const STATUS_BAD_REQUEST = 8;
    /**
     * Status of the response
     */
    public $status;
    /**
     * Body of the response
     */
    public $body;

}

?>
