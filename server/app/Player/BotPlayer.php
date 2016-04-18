<?php
namespace App\Player;

class BotPlayer extends Player
{
	public function __construct()
	{
		parent::__construct();
	}

	public function isHuman()
	{
		return false;
	}
}