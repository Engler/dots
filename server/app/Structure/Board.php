<?php
namespace App\Structure;

use App\Structure\Square;

class Board
{
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