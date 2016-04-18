<?php
namespace App;

trait ContainsGameSessionTrait
{
	protected $session;

	public function getSession()
	{
		return $this->session;
	}

	public function setSession($session)
	{
		$this->session = $session;
	}
}