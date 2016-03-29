<html>
	<head>
		<title>Jogo dos pontos</title>
		<script type="text/javascript" src="assets/js/jquery-2.2.1.min.js"></script>
		<script type="text/javascript" src="assets/js/main.js"></script>
		<link href="assets/css/main.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="header">
			<h1>Jogo dos pontos</h1>
			<div class="score left">
				<div class="name">Voce</div>
				<div class="points">0</div>
			</div>
			<div class="score right">
				<div class="name">Adversario</div>
				<div class="points">0</div>
			</div>
		</div>
		<div id="board">
			<table cellpadding="0" cellspacing="0">
				<?php
					$rows = 8;
					$columns = 15;

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