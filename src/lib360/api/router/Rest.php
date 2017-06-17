<?php

namespace spoof\lib360\api\router;

use spoof\lib360\api;

/**
 * Simple RESTful API handler
 */
class Rest implements api\IRouter
{

    /**
     * Internal map of request methods to object methods
     */
    protected $methodMap = array(
        'GET' => 'get',
        'POST' => 'submit',
        'PUT' => 'set',
        'DELETE' => 'delete',
    );

    /**
     * Internal storage for namespace string
     */
    protected $namespace;

    /**
     * Internal storage for configuration
     */
    protected $config;

    /**
     * Constructor, sets configuration
     *
     * @param string $namespace namespace (without precceding backslashes)
     * @param array $config configuration array, optional, default NULL
     */
    public function __construct($namespace, array $config = null)
    {
        $this->namespace = $namespace;
        $this->config = $config;
    }

    /**
     * Main method that handles the request
     *
     * Steps performed:
     * - translates request object into application execution path
     * - runs application module
     * - returns response in a standardized object
     *
     * @param api\Request $request request object
     *
     * @return api\Response response object
     */
    public function handleRequest(api\Request $request)
    {
        $response = new api\Response();
        try {
            // map request to class and method
            list($class, $method, $args) = $this->translateRequest($request);
            // make sure class and method exist
            if (!class_exists($class) || !method_exists($class, $method)) {
                throw new api\ResourceNotFoundException('Resource not found');
            }
            $module = new $class($this->config);
            // execute called function
            $response->body = call_user_func_array(array($module, $method), $args);
            //TODO this needs to be different 2xx status based on what happened
            $response->status = api\Response::STATUS_OK;
        } catch (\InvalidArgumentException $e) {
            $response->status = api\Response::STATUS_BAD_REQUEST;
            $response->body = $e->getMessage();
        } catch (api\ResourceNotFoundException $e) {
            $response->status = api\Response::STATUS_RESOURCE_NOT_FOUND;
            $response->body = $e->getMessage();
        }
        // return result
        return $response;
    }

    /**
     * Internal function that translates request object to execution path
     *
     * @param api\Request $request request object
     *
     * @return array parsed result
     *    0 => string class
     *    1 => string method
     *    2 => array arguments
     *
     * @throws api\ResourceNotFoundException when request has too few parts
     */
    protected function translateRequest(api\Request $request)
    {
        $class = '\\' . $this->namespace . '\\';
        $args = array();
        switch (count($request->parts)) {
            case 0:
                // if request has no parts, we can't find the resource
                throw new api\ResourceNotFoundException('No resource specified in request');
                break;
            case 1:
                // if there's only one part, that's the class name
                $class .= $request->parts[0];
                $method = $this->translateOperation($request->operation);
                break;
            case 2:
                // if there are 2 parts, that's class and method
                $class .= $request->parts[0];
                $method = $this->translateOperation($request->operation) .
                    $request->parts[1];
                break;
            default:
                // request has 3 or more parts
                $parts = $request->parts;
                $args[] = array_pop($parts);
                $method = $this->translateOperation($request->operation) .
                    array_pop($parts);
                $class .= implode('\\', $parts);
        }
        $args += $request->data;
        return array($class, $method, $args);
    }

    /**
     * Internal function that translates request method to object method
     */
    protected function translateOperation($operation)
    {
        if (!isset($this->methodMap[$operation])) {
            throw new \BadMethodCallException("Method $operation is not implemented.");
        }
        return $this->methodMap[$operation];
    }

}

?>
