<?php

namespace lib360\net\erlang\peb;

/**
*	Node class is used to set up an Erlang node
*/
class Node
{

	/**
	*	Internal storage for the node process
	*/
	protected $link;

	/**
	*	Internal storage for remote node name
	*/
	protected $remoteNode;

	/**
	*	Internal storage for Erlang secret
	*/
	protected $secret;

	/**
	*	Constructor sets remote node and Erlang secret
	*	@param string $remoteNode valid Erlang node name
	*	@param string $secret Erlang secret
	*/
	public function __construct($remoteNode, $secret)
	{
		$this->remoteNode = $remoteNode;
		$this->secret = $secret;
	}

	/**
	*	Connects to specified node
	*	@param boolean $persistent
	*	@return resource process
	*/
	public function connect($persistent = FALSE)
	{
		if ($persistent)
		{
			$this->link = peb_pconnect($this->remoteNode, $this->secret);
		}
		else
		{
			$this->link = peb_connect($this->remoteNode, $this->secret);
		}
		return $this->link;
	}

	/**
	*	Disconnects an active connection
	*/
	public function disconnect()
	{
		return peb_close($this->link);
	}

	/**
	*	Gets internal Erlang PID of the node
	*	@return resource process
	*/
	public function getPID()
	{
		return $this->link;
	}

	/**
	*	Sends message to another named process
	*	@param Message $message message object to send
	*	@param string $name name of the process to send the message to
	*/
	public function sendMessageByName(Message $message, $name)
	{
		$msg = $this->prepareMessage($message);
		peb_send_byname($name, $msg, $this->link);
	}

	/**
	*	Sends message to another process by ID
	*	@param Message $message message object to send
	*	@param resource $pid PID resource to send message to
	*/
	public function sendMessageByPid(Message $message, $pid)
	{
		$msg = $this->prepareMessage($message);
		peb_send_bypid($pid, $msg, $this->link);
	}

	/**
	*	Internal function to prepare message object prior to sending
	*	@param Message $message object to prepare
	*	@return resource encoded message
	*/
	protected function prepareMessage(Message $message)
	{
		list($format, $values) = $message->getPebArgs();
		return peb_vencode($format, $values);
	}

	/**
	*	Internal function to prepare message object as an argument
	*	@param Message $message object to prepare
	*	@return resource encoded message
	*/
	protected function prepareArgument(Message $message)
	{
		list($format, $values) = $message->getPebArgs();
		return peb_encode($format, $values);
	}

	/**
	*	Get any received messages
	*	@return mixed decoded received message
	*/
	public function receive()
	{
		return peb_vdecode(peb_receive($this->link));
	}

	/**
	*	Make an MFA call
	*	@param string $module erlang module
	*	@param string $function erlang module function
	*	@param Message $message message object to use as argument
	*	@return mixed decoded result message
	*/
	public function rpc($module, $function, Message $args)
	{
		$msg = $this->prepareArgument($args);
		return peb_vdecode(peb_rpc($module, $function, $msg, $this->link));
	}

}

?>