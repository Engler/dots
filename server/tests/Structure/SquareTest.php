<?php
namespace Structure;

use App\Structure\Square;
use App\Player\HumanPlayer;

class SquareTest extends \PHPUnit_Framework_TestCase
{
	public function testFillEdge()
	{
		$player = new HumanPlayer();
	    $square = new Square(1, 1);

	    $square->fillEdge($player, Square::TOP);
	    $this->assertTrue($square->isEdgeFilled(Square::TOP));
	    $this->assertFalse($square->finished());
	    $this->assertFalse($square->aboutToFinish());

	    $square->fillEdge($player, Square::RIGHT);
	    $this->assertTrue($square->isEdgeFilled(Square::RIGHT));
	    $this->assertFalse($square->finished());
	    $this->assertFalse($square->aboutToFinish());

	    $square->fillEdge($player, Square::BOTTOM);
	    $this->assertTrue($square->isEdgeFilled(Square::BOTTOM));
	    $this->assertFalse($square->finished());
	    $this->assertTrue($square->aboutToFinish());

	    $square->fillEdge($player, Square::LEFT);
	    $this->assertTrue($square->isEdgeFilled(Square::LEFT));
	    $this->assertTrue($square->finished());
	    $this->assertFalse($square->aboutToFinish());
	}

	public function testRemainingEdges()
	{
		$player = new HumanPlayer();
	    $square = new Square(1, 1);

	    $square->fillEdge($player, Square::TOP);
	    $this->assertEquals($square->getRemainingEdges(), [
	    	Square::LEFT, Square::BOTTOM, Square::RIGHT
    	]);

    	$square->fillEdge($player, Square::LEFT);
    	$this->assertEquals($square->getRemainingEdges(), [
	    	Square::BOTTOM, Square::RIGHT
    	]);

    	$square->fillEdge($player, Square::RIGHT);
    	$this->assertEquals($square->getRemainingEdges(), [
	    	Square::BOTTOM
    	]);

    	$square->fillEdge($player, Square::BOTTOM);
    	$this->assertEquals($square->getRemainingEdges(), []);
	}
}