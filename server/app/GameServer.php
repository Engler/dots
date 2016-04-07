<?php
namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use App\GameSession;

class GameServer implements MessageComponentInterface
{

	protected $connections;
	protected $sessions;

	public function __construct()
	{
		$this->connections = [];
		$this->sessions = [];

		echo "Running...\n";
	}

	public function onOpen(ConnectionInterface $connection)
	{
		$connectionId = strtoupper(uniqid());
		$connection->connectionId = $connectionId;

        echo "New connection " . $connectionId . "\n";
    }

    public function onMessage(ConnectionInterface $connection, $message)
    {
    	if (!isset($connection->sessionId)) {
	    	$session = new GameSession([
	    		'type'   => GameSession::HUMAN_VS_BOT,
	    		'width'  => 5,
	    		'height' => 5
			]);

	    	$this->sessions[$session->getId()] = $session;
			$connection->sessionId = $session->getId();

			echo "Initialized session " . $session->getId() . "\n";
    	}
    }

    public function onClose(ConnectionInterface $connection)
    {
        echo "Connection disconnected\n";
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $connection->close();
    }

}