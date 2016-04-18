<?php
namespace App\Player;

abstract class Player
{
	use \App\ContainsGameSessionTrait;
	
	protected $session;

	public function __construct()
	{
		
	}

	abstract public function isHuman();
}