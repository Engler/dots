<?php

	$width = isset($_GET['w']) ? (int) $_GET['w'] : 5;
	$height = isset($_GET['h']) ? (int) $_GET['h'] : 5;

	if ($width < 2) { $width = 2; }
	if ($height < 2) { $height = 2; }

	$desiredSquareWidth = 80;
	$maxWidth = 1000;
	$minWidth = 350;

	$boardWidth = $desiredSquareWidth * $width;
	if ($boardWidth > $maxWidth) {
		$boardWidth = $maxWidth;
	} else if ($boardWidth < $minWidth) {
		$boardWidth = $minWidth;
	}

?>
<html>
	<head>
		<title>Jogo dos pontos</title>
		<script type="text/javascript" src="assets/js/jquery-2.2.1.min.js"></script>
		<script type="text/javascript" src="assets/js/main.js"></script>
		<link href="assets/css/main.css" rel="stylesheet" type="text/css" />
		<script>
		var BOARD_WIDTH = <?php echo $width; ?>;
		var BOARD_HEIGHT = <?php echo $height; ?>;
		</script>
	</head>
	<body>
		<div id="header">
			<h1>Jogo dos pontos</h1>
			<div class="score left">
				<div class="name">Você</div>
				<div id="human-points" class="points">0</div>
			</div>
			<div class="score right">
				<div class="name">BOT</div>
				<div id="bot-points" class="points">0</div>
			</div>
		</div>
		<div id="board" class="loading" style="width: <?php echo $boardWidth; ?>px;">
			<div class="loading-screen"></div>
			<div class="finished-screen"><div class="finished-message"></div></div>

			<table cellpadding="0" cellspacing="0">
				<?php
					for ($row = 1; $row <= $height; $row++) {
						echo '<tr>';
						for ($column = 1; $column <= $width; $column++) {
							echo '<td>
									'.($row == 1 && $column == 1 ? '<div class="dot dot-top-left"></div>' : '').'
									'.($row == 1 ? '<div class="dot dot-top-right"></div>' : '').'
									'.($column == 1 ? '<div class="dot dot-bottom-left"></div>' : '').'
									<div class="dot dot-bottom-right"></div>

									'.($column == 1 ? '<div class="line left-line"></div>' : '').'
									'.($row == 1 ? '<div class="line top-line"></div>' : '').'
									<div class="line right-line"></div>
									<div class="line bottom-line"></div>

								  	<div id="square-'.$column.'-'.$row.'" data-row="'.$row.'" data-column="'.$column.'" class="square">
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