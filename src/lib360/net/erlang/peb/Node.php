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

namespace spoof\lib360\net\erlang\peb;

/**
 * Node class is used to set up an Erlang node
 */
class Node
{
    /**
     * Internal storage for the node process
     */
    protected $link;

    /**
     * Internal storage for remote node name
     */
    protected $remoteNode;

    /**
     * Internal storage for Erlang secret
     */
    protected $secret;

    /**
     * Constructor sets remote node and Erlang secret.
     *
     * @param string $remoteNode valid Erlang node name
     * @param string $secret Erlang secret
     */
    public function __construct($remoteNode, $secret)
    {
        $this->remoteNode = $remoteNode;
        $this->secret = $secret;
    }

    /**
     * Connects to specified node.
     *
     * @param boolean $persistent
     * @return resource process
     */
    public function connect($persistent = false)
    {
        if ($persistent) {
            $this->link = peb_pconnect($this->remoteNode, $this->secret);
        } else {
            $this->link = peb_connect($this->remoteNode, $this->secret);
        }
        return $this->link;
    }

    /**
     * Disconnects an active connection
     */
    public function disconnect()
    {
        return peb_close($this->link);
    }

    /**
     * Gets internal Erlang PID of the node.
     *
     * @return resource process
     */
    public function getPID()
    {
        return $this->link;
    }

    /**
     * Sends message to another named process.
     *
     * @param Message $message message object to send
     * @param string $name name of the process to send the message to
     */
    public function sendMessageByName(Message $message, $name)
    {
        $msg = $this->prepareMessage($message);
        peb_send_byname($name, $msg, $this->link);
    }

    /**
     * Internal function to prepare message object prior to sending.
     *
     * @param Message $message object to prepare
     *
     * @return resource encoded message
     */
    protected function prepareMessage(Message $message)
    {
        list($format, $values) = $message->getPebArgs();
        return peb_vencode($format, $values);
    }

    /**
     * Sends message to another process by ID.
     *
     * @param Message $message message object to send
     * @param resource $pid PID resource to send message to
     */
    public function sendMessageByPid(Message $message, $pid)
    {
        $msg = $this->prepareMessage($message);
        peb_send_bypid($pid, $msg, $this->link);
    }

    /**
     * Get any received messages.
     *
     * @return mixed decoded received message
     */
    public function receive()
    {
        return peb_vdecode(peb_receive($this->link));
    }

    /**
     * Make an MFA call.
     *
     * @param string $module erlang module
     * @param string $function erlang module function
     * @param Message $message message object to use as argument
     *
     * @return mixed decoded result message
     */
    public function rpc($module, $function, Message $args)
    {
        $msg = $this->prepareArgument($args);
        return peb_vdecode(peb_rpc($module, $function, $msg, $this->link));
    }

    /**
     * Internal function to prepare message object as an argument.
     *
     * @param Message $message object to prepare
     *
     * @return resource encoded message
     */
    protected function prepareArgument(Message $message)
    {
        list($format, $values) = $message->getPebArgs();
        return peb_encode($format, $values);
    }

}

?>
