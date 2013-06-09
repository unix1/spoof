<?php

namespace lib360\net\http\session;

/*
    This is Spoof.
    Copyright (C) 2013  Spoof project.

    Spoof is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Spoof is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Spoof.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
*	Session Handler implementation
*	Uses PHP Erlang bridge to store sessions in a Key Value Store
*/

class PebKvStore implements \SessionHandlerInterface
{

	/**
	*	Internal property for address of Erlang node
	*/
	protected $address;

	/**
	*	Internal property for kvstore application
	*/
	protected $application;

	/**
	*	Internal property for peb link resource
	*/
	protected $link;

	/**
	*	Internal property to store Erlang Node object
	*/
	protected $node;

	/**
	*	Internal property to store secret for connecting to Erlang node
	*/
	protected $secret;

	/**
	* Internal property to store kvstore server name
	*/
	protected $server;

	/**
	*	Constructor, sets initial values, including randomly generated server name
	*	@param string $address address of Erlang node to connect to
	*	@param string $secret Erlang secret to use during connection
	*	@param string $application name of kvstore application
	*/
	public function __construct($address, $secret, $application = 'kvstore')
	{
		$this->address = $address;
		$this->secret = $secret;
		$this->application = $application;
		$this->server = 'kv_' . \lib360\crypt\Random::getString(3, FALSE, FALSE);
	}

	/**
	*	Opens session, implementation of \SessionHandlerInterface::open()
	*	@param string $savePath this is unused in this implementation
	*	@param string $sessionName this is unused in this implementation
	*	@return boolean TRUE
	*/
	public function open($savePath, $sessionName)
	{
		$this->node = new \lib360\net\erlang\peb\Node($this->address, $this->secret);
		$this->link = $this->node->connect();
		/// TODO this name could be generated on the server side
		$args = array(new \lib360\net\erlang\peb\value\Primitive($this->server, \lib360\net\erlang\peb\value\Type::ATOM));
		$message = new \lib360\net\erlang\peb\Message($args);
		$this->node->rpc($this->application, 'start_server', $message);
		return TRUE;
	}

	/**
	*	Closes session, disconnects Erlang node, implementation of \SessionHandlerInterface::close()
	*	@return boolean TRUE
	*/
	public function close()
	{
		$args = array(new \lib360\net\erlang\peb\value\Primitive($this->server, \lib360\net\erlang\peb\value\Type::ATOM));
		$message = new \lib360\net\erlang\peb\Message($args);
		$this->node->rpc($this->application, 'stop_server', $message);
		$this->node->disconnect();
		return TRUE;
	}

	/**
	*	Reads session ID, implementation of \SessionHandlerInterface::read()
	*	@param string $id existing session ID to retrieve
	*	@return string serialized PHP session data
	*/
	public function read($id)
	{
		$args = array(
			new \lib360\net\erlang\peb\value\Primitive($this->server, \lib360\net\erlang\peb\value\Type::ATOM),
			new \lib360\net\erlang\peb\value\Primitive($id, \lib360\net\erlang\peb\value\Type::STRING)
		);
		$message = new \lib360\net\erlang\peb\Message($args);
		$result = $this->node->rpc($this->application, 'read', $message);
		return $result[0][1];
	}

	/**
	*	Writes session data to the given session ID, implementation of \SessionHandlerInterface::write()
	*	@param string $id session ID to write to
	*	@param string $data serialized PHP session data
	*	@return boolean TRUE
	*/
	public function write($id, $data)
	{
		$args = array(
			new \lib360\net\erlang\peb\value\Primitive($this->server, \lib360\net\erlang\peb\value\Type::ATOM),
			new \lib360\net\erlang\peb\value\Primitive($id, \lib360\net\erlang\peb\value\Type::STRING),
			new \lib360\net\erlang\peb\value\Primitive($data, \lib360\net\erlang\peb\value\Type::STRING)
		);
		$message = new \lib360\net\erlang\peb\Message($args);
		$result = $this->node->rpc($this->application, 'write', $message);
		return TRUE;
	}

	/**
	*	Deletes session by ID, implementation of \SessionHandlerInterface::destroy()
	*	@param string $id session ID to delete
	*	@return boolean TRUE
	*/
	public function destroy($id)
	{
		$args = array(
			new \lib360\net\erlang\peb\value\Primitive($this->server, \lib360\net\erlang\peb\value\Type::ATOM),
			new \lib360\net\erlang\peb\value\Primitive($id, \lib360\net\erlang\peb\value\Type::STRING)
		);
		$message = new \lib360\net\erlang\peb\Message($args);
		$result = $this->node->rpc($this->application, 'delete', $message);
		return TRUE;
	}

	/**
	*	Garbage collection for sessions, implementation of \SessionHandlerInterface::gc()
	*	@param integer $maxlifetime maximum lifetime of session
	*	@return TRUE
	*/
	public function gc($maxlifetime)
	{
		/*
		* this would need to be:
		* - a cast to a yet unimplemented function; or
		* - implemented on server side entirely
		*/
		return TRUE;
	}

}

?>