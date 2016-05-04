<?php
namespace App\Structure;

use App\Structure\Square;
use App\Event\EventManager;
use App\Logger;

class Board
{
	use \App\ContainsGameSessionTrait;

	protected $width;
	protected $height;
	protected $squares;
	protected $tempSquares;

	public function __construct($width, $height)
	{
		$this->width = $width;
		$this->height = $height;
		$this->tempSquares = false;

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

				if ($this->finished() && !$this->tempSquares) {
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

	/*
	 * Busca por uma aresta próxima a ultima jogada do player humano que não
	 * deixe um quadrado pronto para o player humano fazer, caso não encontre
	 * nenhuma aresta, retorna a aresta que vai conceder a menor quantidade de
	 * pontos ao outro jogador. Caso mais de uma aresta esteja disponível, sorteia
	 * aleatoriamente.
	 */
	public function getNearestAvailableEdge($player, $x, $y, $edge, $radius = 1, $conflictSquares = [])
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
		 			$newItem = ['x' => $s->getX(), 'y' => $s->getY(), 'edge' => $remainingEdge];

		 			$checkResult = $this->willLeftAnySquareAboutToFinish($player, $s->getX(), $s->getY(), $remainingEdge);

		 			if ($checkResult === false) {
		 				$searchEdges[] = $newItem;
		 			} else {
		 				$conflictSquares[] = [
		 					'points' => $checkResult,
		 					'item' => $newItem
						];
		 			}
		 		}
		 	}
		}

		Logger::message($player->getSession(), '[NEAREST] Found ' . count($searchEdges) . ' possible clean nearest edge(s) at radius: ' . $radius . ' of ['.$x.','.$y.','.Square::getEdgeName($edge).']');

		// Se não encontrar nenhuma aresta disponivel...
		if (empty($searchEdges)) {
			// Se o raio de busca ultrapassar os limites do tabuleiro, comeca a considerar as opcoes que geram perda de pontos
			if ($radius > $this->width && $radius > $this->height) {
				Logger::message($player->getSession(), '[CONFLICT] Not found any clean edge to play');
				$min = null;
				foreach ($conflictSquares as $conflictSquare) {
					// Mantém apenas as opcões que geram a menor perda de pontos
					if ($min === null || $conflictSquare['points'] < $min) {
						$min = $conflictSquare['points'];
						$searchEdges = [$conflictSquare['item']];
					}
					// Se existir mais de uma opcão que acaba com a mesma perda de pontos, será escolhida aleatóriamente
					else if ($conflictSquare['points'] == $min) {
						$searchEdges[] = $conflictSquare['item'];
					}
				}

				Logger::message($player->getSession(), '[CONFLICT] Found ' . count($searchEdges) . ' possible edge(s) that can make you lose ' . $min . ' point(s)');
			} else {
				// Aumenta o raio de busca
				$searchEdges = $this->getNearestAvailableEdge($player, $x, $y, $edge, ++$radius, $conflictSquares);
			}
		}

		// Embaralha as opcões encontradas
		shuffle($searchEdges);

		return $searchEdges;
	}

	public function willLeftAnySquareAboutToFinish($player, $desiredX, $desiredY, $edge)
	{
		$tempSquares = [];
		for ($y = 1; $y <= $this->height; $y++) {
			for ($x = 1; $x <= $this->width; $x++) {
				$tempSquares[$x][$y] = clone $this->squares[$x][$y];
			}
		}

		$originalSquares = $this->squares;
		$this->squares = $tempSquares;
		$this->tempSquares = true;

		$this->fill($player, $desiredX, $desiredY, $edge, $squaresFilled);

		$aboutToFinish = $this->getSquareAboutToFinish();

		$result = false;

		if ($aboutToFinish) {
			$result = 0;
			do {
				$result++;
				// Preenche o $aboutToFinish
				$this->fill($player, $aboutToFinish->getX(), $aboutToFinish->getY(), $aboutToFinish->getRemainingEdge(), $squaresFilled);
				$aboutToFinish = $this->getSquareAboutToFinish();
			} while ($aboutToFinish && $squaresFilled > 0);
		}

		$this->squares = $originalSquares;
		$this->tempSquares = false;

		return $result;
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

	public function isTempSquares()
	{
		return $this->tempSquares;
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