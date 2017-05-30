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
	public function __construct($namespace, array $config = NULL) {
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
	public function handleRequest(api\Request $request) {
		// map request to class and method
		$args = array();
		$args[] = array_pop($request->parts);
		$res = array_pop($request->parts);
		$class = '\\' . $this->namespace . implode('\\', $request->parts);
		$method = $this->translateOperation($request->operation) . $res;

		// make sure class and method exist
		$response = new api\Response();
		if (class_exists($class) && method_exists($class, $method)) {
			$module = new $class($this->config);
			$args += $request->data;
			// execute called function
			try {
				$response->body = call_user_func_array(array($module, $method),
					$args);
				//TODO this needs to be different 2xx status based on what happened
				$response->status = api\Response::STATUS_OK;
			} catch (\InvalidArgumentException $e) {
				$response->status = api\Response::STATUS_BAD_REQUEST;
				$response->body = $e->getMessage();
			} catch (api\ResourceNotFoundException $e) {
				$response->status = api\Response::STATUS_RESOURCE_NOT_FOUND;
				$response->body = $e->getMessage();
			}
		} else {
			$response->status = api\Response::STATUS_RESOURCE_NOT_FOUND;
		}

		// return result
		return $response;
	}

	/**
	 * Internal function that translates request method to object method
	 */
	protected function translateOperation($operation) {
		if (!isset($this->methodMap[$operation])) {
			throw new \BadMethodCallException("Method $operation is not implemented.");
		}
		return $this->methodMap[$operation];
	}

}

?>
