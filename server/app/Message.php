<?php
namespace App;

class Message
{
	const RECV_SESSION_START		= 1000;
	const RECV_PLAYER_MOVE			= 1001;
	const RECV_PING					= 1002;

	const SEND_PLAYER_MOVE			= 2000;
	const SEND_PONG					= 2001;
	const SEND_PLAYER_TURN			= 2002;

	private $type;
	private $params;

	public function __construct($type, $params= [])
	{
		$this->type = $type;
		$this->params = $params;
	}

	public function __toString()
	{
		return json_encode(['type' => $this->type, 'params' => $this->params]);
	}
}