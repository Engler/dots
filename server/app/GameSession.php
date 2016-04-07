<?php
namespace App;

use App\Structure\Board;

class GameSession
{
    const HUMAN_VS_BOT   = 1;
    const HUMAN_VS_HUMAN = 2;
    const BOT_VS_BOT     = 3;

    protected $id;
    protected $board;
    protected $type;

	public function __construct($params)
	{
        $this->id = strtoupper(uniqid());
        $this->board = new Board($params['width'], $params['height']);
        $this->type = $params['type'];
	}

    public function getId()
    {
        return $this->id;
    }

}