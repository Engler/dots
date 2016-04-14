var SQUARE_SIZE = 0;
$(document).ready(function() {

	Server.initialize();

	SQUARE_SIZE = $('#board .square').eq(0).width();

	$('#board').click(function() {
		if (!$(this).hasClass('loading')) {
			var line = $('.line.hover');
			if (line.length) {
				var square = line.closest('td').find('.square');

				var x = square.data('column');
				var y = square.data('row');

				var edge = 0;
				if (line.hasClass('bottom-line')) {
					edge = 2;
				} else if (line.hasClass('right-line')) {
					edge = 1;
				} else if (line.hasClass('top-line')) {
					edge = 0;
				} else if (line.hasClass('left-line')) {
					edge = 3;
				}

				$(this).addClass('loading');

				Server.send(1001, {'x' : x, 'y' : y, 'edge' : edge});

				$(this).removeClass('loading');
				line.removeClass('hover');

				//Board.fillEdge(x, y, edge);
			}
		}

		//$('.line.hover').removeClass('hover').addClass('filled');
	});

	$('#board .square').mousemove(function(event) {
		var square = $(event.currentTarget);
		var point = { x : event.offsetX, y : event.offsetY };

		var row = parseInt(square.data('row'));
		var column = parseInt(square.data('column'));

		var squareWidth = Math.floor($('#square-1-1').width());
		var halfSquareWidth = Math.floor(squareWidth / 2);

		$('.line.hover').removeClass('hover');

		if (pointInTriangle(point, {x:0,y:0}, {x:squareWidth,y:0}, {x:halfSquareWidth,y:halfSquareWidth})) {					
			if (row > 1) {
				$('#square-' + column + '-' + (row - 1)).parent().find('.bottom-line').addClass('hover');
			} else {
				square.parent().find('.top-line').addClass('hover');
			}
		} else if(pointInTriangle(point, {x:0,y:squareWidth}, {x:squareWidth,y:squareWidth}, {x:halfSquareWidth,y:halfSquareWidth})) {
			square.parent().find('.bottom-line').addClass('hover');
		} else if(pointInTriangle(point, {x:0,y:0}, {x:0,y:squareWidth}, {x:halfSquareWidth,y:halfSquareWidth})) {
			if (column > 1) {
				$('#square-' + (column - 1) + '-' + row).parent().find('.right-line').addClass('hover');
			} else {
				square.parent().find('.left-line').addClass('hover');
			}
		} else if(pointInTriangle(point, {x:squareWidth,y:0}, {x:squareWidth,y:squareWidth}, {x:halfSquareWidth,y:halfSquareWidth})) {
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

var Board = new function() {

	this.fillEdge = function(x, y, edge) {

		// Right, Bottom
		if (edge == 1 || edge == 2) {
			var element = $('#square-' + x + '-' + y).closest('td');

			if (edge == 1) {
				element.find('.right-line').addClass('filled');
			} else if (edge == 2) {
				element.find('.bottom-line').addClass('filled');
			}
		}
		// Top
		else if (edge == 0) {
			var element = null;

			if (y > 1) {
				element = $('#square-' + x + '-' + (y - 1)).closest('td');
				element.find('.bottom-line').addClass('filled');
			} else {
				element = $('#square-' + x + '-' + y).closest('td');
				element.find('.top-line').addClass('filled');
			}
		}
		// Left
		else if (edge == 3) {
			var element = null;

			if (x > 1) {
				element = $('#square-' + (x - 1) + '-' + y).closest('td');
				element.find('.right-line').addClass('filled');
			} else {
				element = $('#square-' + x + '-' + y).closest('td');
				element.find('.left-line').addClass('filled');
			}
		}

	}

}

var Server = new function() {
    this.socket = null;
    
    var $this = this;
    
    this.initialize = function() {
        
        if ($this.isConnected()) {
            return false;
        }
        
        this.socket = new WebSocket("ws://localhost:8080/");
        
        this.socket.onopen = function() {
            console.log('socket opened');
            $this.send(1000);
        };
        
        this.socket.onclose = function(event) {
            console.log('socket closed');
        };
        this.socket.onerror = function (error) {
            console.log(error);
        };
        this.socket.onmessage = function(event) {
            var messageData = JSON.parse(event.data);
            if (messageData.type == 2000) {
            	$this.receivePlayerMove(messageData.params);
            }
        };
        
        return true;
    };
    
    this.isConnected = function() {
        return (this.socket != null && this.socket.readyState == 1);
    };
    
    this.close = function() {
        this.socket.close();
    }

    this.send = function(type, params) {
    	params = params || null;
        this.socket.send(JSON.stringify({'type':type, 'params':params}));
    };

    this.receivePlayerMove = function(params) {
    	Board.fillEdge(params.x, params.y, params.edge);
    };
}; 