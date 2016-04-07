<?php
namespace App\Structure;

class Square
{
	const TOP    = 0;
	const RIGHT  = 1;
	const BOTTOM = 2;
	const LEFT   = 3;

	private $edgeValues = [1, 2, 4, 8];

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

	public function fillEdge($edge)
	{
		if (!$this->isEdgeFilled($edge)) {
			$this->edges[$edge] = true;
			$this->value += $this->getEdgeValue($edge);
			return true;
		}
		return false;
	}

	public function getEdgeValue($edge)
	{
		return $this->edgeValues[$edge];
	}

	public function finished()
	{
		return $this->owner !== null;
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