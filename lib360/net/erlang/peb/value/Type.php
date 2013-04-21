<?php

namespace lib360\net\erlang\peb\value;

class Type
{

	/**
	*	@todo separate primitive vs collection types somewhere
	*/
	const ATOM = 1;
	const BINARY = 2;
	const DOUBLE = 3;
	const FLOAT = 4;
	const INTEGER = 5;
	const LONG = 6;
	const PID = 7;
	const STRING = 8;
	const UNSIGNED = 9;
	const LLIST = 10;
	const TUPLE = 11;

	public static $format = array(
		self::ATOM => '~a',
		self::BINARY => '~b',
		self::DOUBLE => '~d',
		self::FLOAT => '~f',
		self::INTEGER => '~i',
		self::LONG => '~l',
		self::PID => '~p',
		self::STRING => '~s',
		self::UNSIGNED => '~u',
		self::LLIST => array('start' => '[', 'end' => ']', 'separator' => ','),
		self::TUPLE => array('start' => '{', 'end' => '}', 'separator' => ','),
	);

}

?>