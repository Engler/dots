<?php
namespace App\Player;

class HumanPlayer extends Player
{
	protected $connection;

	public function __construct()
	{
		parent::__construct();
	}

	public function isHuman()
	{
		return true;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function setConnection($connection)
	{
		$this->connection = $connection;
	}
}