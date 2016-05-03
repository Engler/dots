<?php
namespace App\Structure;

use App\Structure\Square;
use App\Event\EventManager;

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

				if ($this->finished()) {
					EventManager::fire('game.finished', [
			            'session' => $player->getSession(),
			            'winner' => $this->getWinner()
			        ]);
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

	public function getNearestAvailableEdge($x, $y, $edge, $radius = 1)
	{
		$search = [];

		if ($radius == 1) {
			if ($edge == Square::TOP || $edge == Square::BOTTOM) {
				// Pega os quadrados vizinhos da mesma linha
				for ($r = -$radius; $r <= $radius; $r += 1) { $search[] = $this->getSquare($x + $r, $y); }

				if ($edge == Square::TOP) {
					// Pega os quadrados vizinhos da linha de cima
					for ($r = -$radius; $r <= $radius; $r += 1) { $search[] = $this->getSquare($x + $r, $y - $radius); }
				} else {
					// Pega os quadrados vizinhos da linha de baixo
					for ($r = -$radius; $r <= $radius; $r += 1) { $search[] = $this->getSquare($x + $r, $y + $radius); }
				}
			} else {
				// Pega os quadrados vizinhos da mesma coluna
				for ($r = -$radius; $r <= $radius; $r += 1) { $search[] = $this->getSquare($x, $y + $r); }

				if ($edge == Square::LEFT) {
					// Pega os quadrados vizinhos da coluna da esquerda
					for ($r = -$radius; $r <= $radius; $r += 1) { $search[] = $this->getSquare($x - $radius, $y + $r); }
				} else {
					// Pega os quadrados vizinhos da coluna da direita
					for ($r = -$radius; $r <= $radius; $r += 1) { $search[] = $this->getSquare($x + $radius, $y + $r); }
				}
			}
		} else {
			// Usar esses offsets depois
			$offsetStartX = $offsetEndX = $offsetStartY = $offsetEndY = 0;

			switch ($edge) {
				case Square::LEFT:
					$offsetEndX = -1;
					break;
				case Square::RIGHT:
					$offsetStartX = 1;
					break;
				case Square::TOP:
					$offsetEndY = -1;
					break;
				case Square::BOTTOM:
					$offsetStartY = 1;
					break;
			}

			// Pega a linha de quadrados TOP
			for ($r = -$radius + $offsetStartX; $r <= $radius + $offsetEndX; $r += 1) { $search[] = $this->getSquare($x + $r, $y - $radius + $offsetStartY); }

			// Pega a linha de quadrados BOTTOM
			for ($r = -$radius + $offsetStartX; $r <= $radius + $offsetEndX; $r += 1) { $search[] = $this->getSquare($x + $r, $y + $radius + $offsetEndY); }

			// Pega a linha de quadrados LEFT
			for ($r = -$radius + $offsetStartY; $r <= $radius + $offsetEndY; $r += 1) { $search[] = $this->getSquare($x - $radius + $offsetStartX, $y + $r); }

			// Pega a linha de quadrados RIGHT
			for ($r = -$radius + $offsetStartY; $r <= $radius + $offsetEndY; $r += 1) { $search[] = $this->getSquare($x + $radius + $offsetEndX, $y + $r); }
		}

		$searchEdges = [];

		// Remove os quadrados nulos (que estão fora das bordas)
		foreach ($search as $k => $s) {
		 	if ($s === null) {
		 		unset($search[$k]);
		 	} else {
		 		foreach ($s->getRemainingEdges() as $remainingEdge) {
		 			$searchEdges[] = ['x' => $s->getX(), 'y' => $s->getY(), 'edge' => $remainingEdge];
		 		}
		 	}
		}

		// Se não encontrar nenhuma aresta disponivel, aumenta o raio de busca
		if (empty($searchEdges)) {
			$searchEdges = $this->getNearestAvailableEdge($x, $y, $edge, ++$radius);
		}

		//shuffle($searchEdges);

		return $searchEdges;
	}

	public function finished()
	{
		for ($y = 1; $y <= $this->height; $y++) {
			for ($x = 1; $x <= $this->width; $x++) {
				if (!$this->squares[$x][$y]->finished()) {
					return false;
				}
			}
		}
		return true;
	}

	// 1 = Human, 0 = Empate, -1 = BOT
	public function getWinner()
	{
		if (!$this->finished()) {
			return 0;
		}

		$humanPoints = 0;
		$botPoints = 0;

		for ($y = 1; $y <= $this->height; $y++) {
			for ($x = 1; $x <= $this->width; $x++) {
				if ($this->squares[$x][$y]->getOwner()->isHuman()) {
					$humanPoints++;
				} else {
					$botPoints++;
				}
			}
		}

		if ($humanPoints > $botPoints) {
			return 1;
		} else if ($humanPoints < $botPoints) {
			return -1;
		}

		return 0;
	}

	public function getWidth()
	{
		return $this->width;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function highlightSquares($squares)
	{
		$string = '';
		for ($y = 1; $y <= $this->height; $y++) {
			for ($x = 1; $x <= $this->width; $x++) {
				$exist = false;
				foreach ($squares as $square) {
					if ($square['x'] == $x && $square['y'] == $y) {
						$exist = true;
						break;
					}
				}
				$string .= '[' . ($exist ? 'x' : ' ') . ']';
			}
			$string .= "\n";
		}
		return $string;
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