<?php
namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use App\GameSession;
use App\Message;
use App\Logger;

class GameServer implements MessageComponentInterface
{

	protected $connections;
	protected $sessions;

	public function __construct()
	{
		$this->connections = [];
		$this->sessions = [];

		Logger::message('Initialized...');
	}

	public function onOpen(ConnectionInterface $connection)
	{
		$connectionId = strtoupper(uniqid('CONN-'));
		$connection->connectionId = $connectionId;

        Logger::message('New connection: ' .  $connection->connectionId);
    }

    public function onMessage(ConnectionInterface $connection, $message)
    {
    	$message = json_decode($message);
    	if ($message) {
    		$params = isset($message->params) ? $message->params : [];

    		if (!isset($connection->sessionId)) {
    			if ($message->type == Message::RECV_SESSION_START) {
    				$this->receiveSessionStart($connection, $params);
    			}
    		} else {
    			switch ($message->type) {
	    			case Message::RECV_PLAYER_MOVE:
	    				$this->receivePlayerMove($connection, $params);
	    				break;
					case Message::RECV_PING:
	    				$this->receivePing($connection);
	    				break;
	    		}
    		}
    	}

    	/*
		Se nao exister uma sessao
			Cria a sessao do jogo
		Senao
			Se eh uma mensagem de jogada, e realmente eh a vez desse jogador
		*/
    }

    public function onClose(ConnectionInterface $connection)
    {
        Logger::message('Connection closed: ' .  $connection->connectionId);
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        Logger::message('An error has occurred: ' .  $e->getMessage());
        $connection->close();
    }

    public function receiveSessionStart($connection, $params)
    {
		$session = new GameSession([
    		'type'   => GameSession::HUMAN_VS_BOT,
    		'width'  => 5,
    		'height' => 5
		]);

    	$this->sessions[$session->getId()] = $session;
		$connection->sessionId = $session->getId();

		Logger::message('Initialized session ' .  $session->getId());
    }

    public function receivePlayerMove($connection, $params)
    {
    	$message = new Message(
    		Message::SEND_PLAYER_MOVE,
    		$params
		);
    	$connection->send((string) $message);

    	Logger::message('X: ' . $params->x . ', Y: ' . $params->y . ', Edge: ' . $params->edge);
    }

    public function receivePing($connection)
    {

    }
}