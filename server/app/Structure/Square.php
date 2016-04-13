<?php
namespace App\Structure;

class Square
{
	const TOP    = 0;
	const RIGHT  = 1;
	const BOTTOM = 2;
	const LEFT   = 3;

	private static $edgeValues = [1, 2, 4, 8];

	protected $x;
	protected $y;
	protected $value;
	protected $edges;
	protected $owner;

	public function __construct($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
		$this->value = 0;
		$this->edges = [false, false, false, false];
		$this->owner = null;
	}

	public function isEdgeFilled($edge)
	{
		return $this->edges[$edge];
	}

	public function fillEdge($player, $edge)
	{
		if (!$this->isEdgeFilled($edge)) {
			$this->edges[$edge] = true;
			$this->value += self::getEdgeValue($edge);

			if ($this->finished()) {
				$this->owner = $player;
			}

			return true;
		}
		return false;
	}

	public static function getEdgeValue($edge)
	{
		return self::$edgeValues[$edge];
	}

	public static function getOppositeEdge($edge)
	{	
		switch ($edge) {
			case self::TOP: return self::BOTTOM;
			case self::BOTTOM: return self::TOP;
			case self::LEFT: return self::RIGHT;
			case self::RIGHT: return self::LEFT;
		}
	}

	public function setOwner($owner)
	{
		$this->owner = $owner;
	}

	public function finished()
	{
		return $this->value === 15;
	}

	public function aboutToFinish()
	{
		return in_array($this->value, [7, 11, 13, 14]);
	}

	public function __toString()
	{
		return '[' . str_pad($this->value, 3, ' ', STR_PAD_BOTH) . ']';
	}
}