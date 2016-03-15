<html>
	<head>
		<title>The DOT Game!</title>
		<script type="text/javascript" src="assets/js/jquery-2.2.1.min.js"></script>
		<script type="text/javascript">
			var SQUARE_SIZE = 0;

			$(document).ready(function() {

				SQUARE_SIZE = $('#board .square').eq(0).width();

				$('#board').click(function() {
					$('.line.hover').removeClass('hover').addClass('player-1');
				});

				$('#board .square').mousemove(function(event) {
					var square = $(event.currentTarget);
					var point = { x : event.offsetX, y : event.offsetY };

					var row = parseInt(square.data('row'));
					var column = parseInt(square.data('column'));

					$('.line.hover').removeClass('hover');

					if (pointInTriangle(point, {x:0,y:0}, {x:60,y:0}, {x:30,y:30})) {					
						if (row > 1) {
							$('#square-' + (row - 1) + '-' + column).parent().find('.bottom-line').addClass('hover');
						} else {
							square.parent().find('.top-line').addClass('hover');
						}
					} else if(pointInTriangle(point, {x:0,y:60}, {x:60,y:60}, {x:30,y:30})) {
						square.parent().find('.bottom-line').addClass('hover');
					} else if(pointInTriangle(point, {x:0,y:0}, {x:0,y:60}, {x:30,y:30})) {						
						if (column > 1) {
							$('#square-' + row + '-' + (column - 1)).parent().find('.right-line').addClass('hover');
						} else {
							square.parent().find('.left-line').addClass('hover');
						}
					} else if(pointInTriangle(point, {x:60,y:0}, {x:60,y:60}, {x:30,y:30})) {
						square.parent().find('.right-line').addClass('hover');
					}

				});
			});

			function pointInTriangle(point, v1, v2, v3) {
			    var b1 = signTriangle(point, v1, v2) < 0;
			    var b2 = signTriangle(point, v2, v3) < 0;
			    var b3 = signTriangle(point, v3, v1) < 0;

				return ((b1 == b2) && (b2 == b3));
			}

			function signTriangle(p1, p2, p3) {
				return (p1.x - p3.x) * (p2.y - p3.y) - (p2.x - p3.x) * (p1.y - p3.y);   
			}
		</script>
		<style type="text/css">
		body {
			background: #F0F0F0;
			font-family: Arial;
			text-align: center;
		}
		h1 {
			color: #555;
			font-size: 30px;
			margin-top: 30px;
			padding-bottom: 20px;
		}
		#board {
			width: 600px;
			background: #FFF;
			margin: 0 auto;
			cursor: pointer;
			padding: 20px;
			border-radius: 10px;
			border: 1px solid #BBB;
			box-shadow: 0px 0px 10px 1px rgba(0,0,0,0.2);
		}
		#board table {
			width: 100%;
		}
		#board table tr td {
			position: relative;
		}
		#board table tr td .line {
			z-index: 10;
			position: absolute;
		}
		#board table tr td .line.hover {
			background: #CCC;
		}
		#board table tr td .line.player-1 { background: #33CC00 !important; }
		#board table tr td .line.player-2 { background: #CC0000 !important; }
		
		#board table tr td .line.top-line { top: -3px; left: 0; right: 0; }
		#board table tr td .line.bottom-line { bottom: -3px; left: 0; right: 0; }
		#board table tr td .line.left-line { left: -3px; top: 0; bottom: 0; }
		#board table tr td .line.right-line { right: -3px; top: 0; bottom: 0; }
		#board table tr td .line.top-line,
		#board table tr td .line.bottom-line {
			height: 5px;
		}
		#board table tr td .line.left-line,
		#board table tr td .line.right-line {
			width: 5px;
		}

		#board table tr td .dot {
			z-index: 11;
			background: #666;
			position: absolute;
			width: 10px;
			height: 10px;
			border-radius: 5px;
		}
		#board table tr td .dot.dot-top-left { top: -5px; left: -5px; }
		#board table tr td .dot.dot-top-right { top: -5px; right: -5px; }
		#board table tr td .dot.dot-bottom-left { bottom: -5px; left: -5px; }
		#board table tr td .dot.dot-bottom-right { bottom: -5px; right: -5px; }

		.square {
			position: relative;
		}
		.square:before {
			display: block;
			content: "";
			width: 100%;
			padding-top: 100%;
		}
		.square > .square-content {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
		}

		</style>
	</head>
	<body>
		<h1>Connect The Dots!</h1>
		<div id="board">
			<table cellpadding="0" cellspacing="0">
				<?php
					$rows = 7;
					$columns = 10;

					for ($row = 1; $row <= $rows; $row++) {
						echo '<tr>';
						for ($column = 1; $column <= $columns; $column++) {
							echo '<td>
									'.($row == 1 && $column == 1 ? '<div class="dot dot-top-left"></div>' : '').'
									'.($row == 1 ? '<div class="dot dot-top-right"></div>' : '').'
									'.($column == 1 ? '<div class="dot dot-bottom-left"></div>' : '').'
									<div class="dot dot-bottom-right"></div>

									'.($column == 1 ? '<div class="line left-line"></div>' : '').'
									'.($row == 1 ? '<div class="line top-line"></div>' : '').'
									<div class="line right-line"></div>
									<div class="line bottom-line"></div>

								  	<div id="square-'.$row.'-'.$column.'" data-row="'.$row.'" data-column="'.$column.'" class="square">
								  		<div class="square-content">
								  		</div>
								  	</div>
								  </td>';
						}
						echo '</tr>';
					}
				?>
			</table>
		</div>
	</body>
</html>