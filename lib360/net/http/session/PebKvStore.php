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

	protected $address;

	protected $application;

	protected $link;

	protected $node;

	protected $secret;

	protected $server;

	public function __construct($address, $secret, $application = 'kvstore')
	{
		$this->address = $address;
		$this->secret = $secret;
		$this->application = $application;
		$this->server = 'kv_' . \lib360\crypt\Random::getString(4, FALSE, FALSE);
	}

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

	public function close()
	{
		$this->node->disconnect();
		return TRUE;
	}

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