<?php
namespace App\Structure;

use App\Structure\Square;

class Board
{
	use \App\ContainsGameSessionTrait;

	protected $width;
	protected $height;
	protected $squares;

	public function __construct($width, $height)
	{
		$this->width = $width;
		$this->height = $height;

		for ($y = 1; $y <= $height; $y++) {
			for ($x = 1; $x <= $width; $x++) {
				$this->squares[$x][$y] = new Square($x, $y);
			}
		}
	}

	public function getSquare($x, $y)
	{
		if ($x < 1 || $y < 1 || $x > $this->width || $y > $this->height) {
			return null;
		}
		
		return $this->squares[$x][$y];
	}

	public function fill($player, $x, $y, $edge, &$squaresFilled)
	{
		$squaresFilled = 0;

		$square = $this->getSquare($x, $y);
		if ($square) {
			$move = $square->fillEdge($player, $edge);
			if ($move) {
				if ($square->finished()) {
					$squaresFilled++;
				}
				$neighborSquare = null;
				switch ($edge) {
					case Square::TOP:
						$neighborSquare = $this->getSquare($x, $y - 1);
						break;
					case Square::BOTTOM:
						$neighborSquare = $this->getSquare($x, $y + 1);
						break;
					case Square::LEFT:
						$neighborSquare = $this->getSquare($x - 1, $y);
						break;
					case Square::RIGHT:
						$neighborSquare = $this->getSquare($x + 1, $y);
						break;
				}
				if ($neighborSquare) {
					$move = $neighborSquare->fillEdge($player, Square::getOppositeEdge($edge));
					if ($move && $neighborSquare->finished()) {
						$squaresFilled++;
					}
				}
				return true;
			}
		}
		return false;
	}

	public function getSquareAboutToFinish()
	{
		for ($y = 1; $y <= $this->height; $y++) {
			for ($x = 1; $x <= $this->width; $x++) {
				if ($this->squares[$x][$y]->aboutToFinish()) {
					return $this->squares[$x][$y];
				}
			}
		}
		return null;
	}

	public function getWidth()
	{
		return $this->width;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function __toString()
	{
		$string = '';
		for ($y = 1; $y <= $this->height; $y++) {
			for ($x = 1; $x <= $this->width; $x++) {
				$string .= (string) $this->squares[$x][$y] . ' ';
			}
			$string .= "\n";
		}
		return $string;
	}
}