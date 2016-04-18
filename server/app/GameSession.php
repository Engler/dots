<?php
namespace App;

use App\Structure\Board;
use App\Event\EventManager;
use App\Player\HumanPlayer;
use App\Player\BotPlayer;

class GameSession
{
    protected $id;
    protected $board;
    protected $type;

    protected $humanPlayer;
    protected $botPlayer;

    protected $turn;

	public function __construct($params)
	{
        $this->id = strtoupper(uniqid('SESS-'));
        $this->board = new Board($params['width'], $params['height']);
        $this->board->setSession($this);

        $this->humanTurn = null;

        $this->humanPlayer = new HumanPlayer();
        $this->humanPlayer->setSession($this);
        $this->humanPlayer->setConnection($params['humanPlayerConnection']);

        $this->botPlayer = new BotPlayer();
        $this->botPlayer->setSession($this);
	}

    public function changeTurn($keepPlayer = false)
    {
        if (!$keepPlayer) {
            if ($this->humanTurn == null) {
                $this->humanTurn = true;
            } else {
                $this->humanTurn = !$this->humanTurn;
            }
        }

        EventManager::fire('game.turnChanged', [
            'session' => $this,
            'humanTurn' => $this->humanTurn
        ]);

        if (!$this->humanTurn) {

            $squaresFilled = 0;
            $square = $this->board->getSquareAboutToFinish();

            if ($square) {
                $x = $square->getX();
                $y = $square->getY();
                $edge = $square->getRemainingEdge();
                $this->board->fill($this->botPlayer, $x, $y, $edge, $squaresFilled);
            }
            
            if ($squaresFilled == 0) {
                $this->changeTurn();
            } else {
                $this->changeTurn(true);
            }
        }
    }

    public function receivePlayerMove($x, $y, $edge)
    {
        $squaresFilled = 0;
        if ($this->board->fill($this->humanPlayer, $x, $y, $edge, $squaresFilled)) {
            if ($squaresFilled == 0) {
                $this->changeTurn();
            }
        }

        $this->changeTurn(true);
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function getHumanPlayer()
    {
        return $this->humanPlayer;
    }

    public function getId()
    {
        return $this->id;
    }

}