<?php
namespace App;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use App\Event\EventManager;
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
        $this->bootstrapEvents();

		Logger::message('Initialized...');
	}

	public function onOpen(ConnectionInterface $connection)
	{
		$connectionId = strtoupper(uniqid('CONN-'));
		$connection->connectionId = $connectionId;

        Logger::message('New connection: ' .  $connection->connectionId);
        EventManager::fire('socket.open');
    }

    public function onMessage(ConnectionInterface $connection, $message)
    {
    	$message = json_decode($message);
    	if ($message) {
            EventManager::fire('socket.message');

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
    }

    public function onClose(ConnectionInterface $connection)
    {
        EventManager::fire('socket.close');
        Logger::message('Connection closed: ' .  $connection->connectionId);
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        EventManager::fire('socket.error');
        Logger::message('An error has occurred: ' .  $e->getMessage());
        $connection->close();
    }

    public function receiveSessionStart($connection, $params)
    {
		$session = new GameSession([
            'width'  => $params->width,
            'height' => $params->height,
            'humanPlayerConnection' => $connection
        ]);

    	$this->sessions[$session->getId()] = $session;
		$connection->sessionId = $session->getId();

		Logger::message($session, 'Initialized session ' .  $session->getBoard()->getWidth() . 'x' . $session->getBoard()->getHeight());

        $session->changeTurn();
    }

    public function receivePlayerMove($connection, $params)
    {
        $session = $this->sessions[$connection->sessionId];
        $session->receivePlayerMove($params->x, $params->y, $params->edge);
    }

    public function receivePing($connection)
    {
    	$message = new Message(Message::SEND_PONG, ['serverTime' => time()]);
    	$connection->send((string) $message);
    }

    public function bootstrapEvents()
    {
        EventManager::on('game.finished', function($event) {
            $message = new Message(Message::SEND_FINISHED, [
                'winner' => $event->winner
            ]);

            $connection = $event->session->getHumanPlayer()->getConnection();
            $connection->send((string) $message);

            Logger::message($event->session, '---- Game finished ----');
        });

        EventManager::on('game.squareFinished', function($event) {
            $x = $event->square->getX();
            $y = $event->square->getY();

            $message = new Message(Message::SEND_SQUARE_FINISHED, [
                'x' => $x,
                'y' => $y,
                'human' => $event->player->isHuman()
            ]);

            $connection = $event->session->getHumanPlayer()->getConnection();
            $connection->send((string) $message);

            Logger::message($event->session, 'Square finished by ' . ($event->player->isHuman() ? 'Human' : 'Bot') . ' at ' . $x . ',' . $y);
        });

        EventManager::on('game.squareFillEdge', function($event) {
            $x = $event->square->getX();
            $y = $event->square->getY();
            $edge = $event->edge;

            $message = new Message(Message::SEND_PLAYER_MOVE, [
                'x' => $x,
                'y' => $y,
                'edge' => $edge
            ]);

            $connection = $event->session->getHumanPlayer()->getConnection();
            $connection->send((string) $message);

            Logger::message($event->session, 'Edge ' . $edge . ' filled by ' . ($event->player->isHuman() ? 'Human' : 'Bot') . ' at ' . $x . ',' . $y);
        });

        EventManager::on('game.turnChanged', function($event) {
            if ($event->humanTurn) {
                $connection = $event->session->getHumanPlayer()->getConnection();
                $message = new Message(Message::SEND_PLAYER_TURN);
                $connection->send((string) $message);
            }
            Logger::message($event->session, 'Turn changed: ' . ($event->humanTurn ? 'Human' : 'Bot'));
        });
    }
}