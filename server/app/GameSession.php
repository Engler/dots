<?php
namespace App;

use App\Structure\Board;
use App\Player\HumanPlayer;
use App\Player\BotPlayer;

class GameSession
{
    const HUMAN_VS_BOT   = 1;
    const BOT_VS_BOT     = 2;

    protected $id;
    protected $board;
    protected $type;
    protected $players;
    protected $turn;

	public function __construct($params)
	{
        $this->id = strtoupper(uniqid());
        $this->board = new Board($params['width'], $params['height']);
        $this->type = $params['type'];
        $this->turn = 0;

        switch ($this->type) {
            case self::HUMAN_VS_BOT:
                $this->players = [new HumanPlayer(), new BotPlayer()];
                break;
            case self::BOT_VS_BOT:
                $this->players = [new BotPlayer(), new BotPlayer()];
                break;
        }
	}

    public function getId()
    {
        return $this->id;
    }

}